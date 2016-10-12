<?php

namespace Model;

use Core\MySql\Mysql_Model\XmMysqlObj;
use Core\Redis\RedisFactory;
use Model\Utils\WebUtils;
use Goutte\Client;

/**
 * author：镇天宇
 * 
 * href连接爬虫，适合并发大量拉取带有过滤条件href
 * 
 * 性能问题:
 *      初期磁盘压力过大，大量的小文件块读写，机械硬盘iops跟不上(带宽只能卡在60m左右徘徊) (已解决!350mbps带宽已完全占满)
 *      初期磁盘压力过大造成php等待mysql响应，造成cpu浪费在mysql网络阻塞上 (已解决)
 *      后期update变多，insert变少，逐渐演变成抓取的带宽瓶颈，但是同样会造成cpu的网络阻塞（已解决）
 * 建议：
 *      针对初期磁盘压力过大，很大一部分原因是热度优先所搜导致硬盘读取，
 *          ->若热度优先搜索用redis代替则可以让抓取瓶颈变为带宽，但是redis没有想到好的解决方案
 *          (已解决！！！利用Redis set去重href，list来维护待抓取href，但不是热度优先搜索，变成广度优先了
 *           但是同样能抓遍全站数据，下一步需要考虑的是Redis状态码的回退问题，不过带宽顺利占满300m，io没有压力)
 * 
 *      若可以采用python抓取更好，应该使用epoll维持响应，Goutte库会造成响应阻塞（需要用多进程弥补）
 *      数据库建议采用Mongodb或者hbase，用mysql维持关系性，采用redis维持href去重列表（不依靠mysql的唯一键） (已解决,Mongodb暂时不需要)
 *      优先优化mysql，尽量减少表索引数量，但要保证select的迅速响应。（目前time索引没有必要，但暂时保留） (利用了Redis删除了三个索引，只留一个href唯一索引)（已解决）
 *      IsHrefLegal函数是高频调用，在确定php的cpu计算压力过大时，优先优化此函数。(待优化，一个进程8%的单核逻辑cpu左右)
 *      在ssd硬盘上效果更好，但是抓取速度也限制在网络带宽上 (已解决)
 */
class HrefSearcher {

    /**
     * 当前的抓取连接
     * @var type 
     */
    private $curr_href;

    /**
     * 策略id
     * @var type 
     */
    private $strategy_id;

    /**
     * 过滤字符串数组
     * @var type 
     */
    private $filter_array;

    /**
     * 数据库句柄
     * @var type 
     */
    private $mysql_obj;

    /**
     * Redis句柄
     * @var type 
     */
    private $redis_obj;

    /**
     * Goutte实例
     * @var type 
     */
    private $client;

    /**
     * 构造函数
     * @param type $strategy_id
     */
    public function __construct($strategy_id) {
        $this->strategy_id = $strategy_id;
        $this->mysql_obj = XmMysqlObj::getInstance();
        $this->redis_obj = RedisFactory::createRedisInstance();
        $this->client = new Client();
    }

    /**
     * 获取批量插入的query语句,用于href的插入
     * @param type $nodes
     * @param type $from_href
     * @return string
     */
    public function getInsertQuery($nodes, $from_href) {
        $query = '';
        $time = time();
        foreach ($nodes as $node) {

            $href = $node->getAttribute('href');

            /* 判断合法性并进行href的过滤整合 */
            if (!$this->isHrefLegal($href)) {
                continue;
            }

            $href = addslashes($href);

            /* 利用Redis Set去重 */
            if ($this->redis_obj->sAdd('unique_href_set', $href)) {

                /* 将href推入队列 */
                $this->redis_obj->sAdd('spider_href_set_' . $this->strategy_id, $href);
                if (!$query) {
                    $query = "insert into search_href "
                            . "(`href`,`from_href`,`strategy_id`,`time`,`num`,`status`) "
                            . "values "
                            . "('$href','$from_href','$this->strategy_id',$time,1,0)";
                } else {
                    $query.=",('$href','$from_href','$this->strategy_id',$time,1,0)";
                }
            } else {
                /* 可以做热度的叠加，后期再考虑 */
            }
        }

        /* 重复数据将num+1，优先值也加1(作废，后期再修改) */
        if ($query) {
            $query.=" ON duplicate KEY UPDATE num=num+1,priority=priority+1";
        }
        return $query;
    }

    /**
     * 判断连接是否合法，合法返回true，否则为false(利用引用顺带过滤)
     * 高频运算（有待算法优化）(一秒大概4w左右的频率)
     * @param type $href
     * @return boolean
     */
    public function isHrefLegal(&$href) {

        if (strpos($href, "http") === FALSE) {

            /* 判断是否可能是简洁路径,若是php和asp等动态不在考虑内，防止对方服务器爆炸 */
            if (strpos($href, "html") !== FALSE || strpos($href, "htm") !== FALSE) {
                $curr_href = $this->curr_href;
                $domain = array();
                preg_match_all('/http:\/\/[^\/]*/', $this->curr_href, $domain);
                /* 根域名 */
                $domain = $domain[0][0];

                if (substr($href, 0, 1) == '/') {
                    /* 当前路径为绝对路径 */
                    $href = $domain . $href;
                } else {
                    /* 当前路径为相对路径 */
                    $explode_array = explode('/', $this->curr_href);
                    $last_string = $explode_array[count($explode_array) - 1];
                    if ($last_string) {
                        $domain_without_http = str_replace("http://", " ", $domain);
                        $domain_without_http = trim($domain_without_http);
                        if ($last_string == $domain_without_http) {
                            $href = $domain . '/' . $href;
                        } else {
                            $href = str_replace($last_string, $href, $curr_href);
                        }
                    } else {
                        $href = $domain . '/' . $href;
                    }
                }

                /* href中/../目录回退过滤 */
                $explode_href_array = explode('/', $href);
                while (in_array('..', $explode_href_array)) {
                    for ($i = 0; $i < count($explode_href_array); $i++) {
                        if ($explode_href_array[$i] == '..') {
                            unset($explode_href_array[$i]);
                            unset($explode_href_array[$i - 1]);
                            $explode_href_array = array_values($explode_href_array);
                            break;
                        }
                    }
                }
                $href = '';
                for ($i = 0; $i < count($explode_href_array); $i++) {
                    if ($i != count($explode_href_array) - 1) {
                        $href.=$explode_href_array[$i] . '/';
                    } else {
                        $href.=$explode_href_array[$i];
                    }
                }
            } else {
                /* 无用href */
                return 0;
            }
        }

        /* href过长过滤,数据库href唯一键值最长190 */
        if (strlen($href) >= 190) {
            return 0;
        }

        /* href满足字符串策略过滤条件,策略根据策略id记录在数据库中 */
        $flag = false;
        if (!$this->filter_array) {
            $query = "select * from search_filter "
                    . "where strategy_id=$this->strategy_id";
            $this->filter_array = $this->mysql_obj->fetch_assoc($query);
        }
        $filter_array = $this->filter_array;
        for ($i = 0; $i < count($filter_array); $i++) {
            if (strpos($href, $filter_array[$i]['string']) !== FALSE) {
                $flag = true;
            }
        }

        if ($flag) {
            /* 最后的href处理放这边
             * 最后href去空，转义字符加反斜杠 */
            $href = trim($href);
            $href = addslashes($href);

            return 1;
        }

        return 0;
    }

    /**
     * 当前抓取href获取策略 ， 采用热度优先搜索(根据连接的重复性判断优先级)
     * @return type
     */
    public function getCurrHref() {

        /* 获取当前需要抓取的href 利用redis队列弹出 */
        $curr_href = $this->redis_obj->sPop('spider_href_set_' . $this->strategy_id);

        if (!$curr_href) {
            /* 取策略的最初href */
            $href = $this->mysql_obj->fetch_assoc_one("select href from search_orgin where strategy_id=$this->strategy_id limit 1");
            $curr_href = $href['href'];
        }

        $curr_href = addslashes($curr_href);

        /* 赋值给属性，用于回退操作 */
        $this->curr_href = $curr_href;

        return $curr_href;
    }

    /**
     * 处理并记录相应的一些内容
     * @param type $crawler
     * @param type $curr_href
     */
    public function recordInfo($crawler, $curr_href) {
        /* 分类添加新闻网页的新闻内容,title等内容 */
        $p_content = call_user_func(array($this->getStrategy(), 'getPContent'), $crawler);
        $title = call_user_func(array($this->getStrategy(), 'getTitle'), $crawler);
        $p_content = WebUtils::toUtf8($p_content);
        $p_content = addslashes($p_content);
        $title = WebUtils::toUtf8($title);
        $query = "update search_href set title='$title',pcontent='$p_content' where href='$curr_href'";
        $this->mysql_obj->exec_query($query);
    }

    public function grabHref($curr_href) {

        $crawler = $this->client->request('GET', $curr_href);

        $this->recordInfo($crawler, $curr_href);

        /* 添加网页中的所有连接,用于下一步的抓取 */
        $nodes = $crawler->filter('a')->getNodes();
        $query = $this->getInsertQuery($nodes, $curr_href);
        $this->mysql_obj->exec_query($query);
    }

    public function Grab() {

        /* 大量操作数据库前,获取当前抓取的连接 */
        $curr_href = $this->getCurrHref();

        /* 操作失败回退 */
        register_shutdown_function(function() {
            $this->redis_obj->sAdd('spider_href_set_' . $this->strategy_id, $this->curr_href);
        });

        /* 大量数据库操作 大概1秒左右响应（包括爬虫的href抓取） */
        $this->grabHref($curr_href);
    }

    /**
     * 抓取循环
     */
    public function startGrab() {

        while (true) {
            $this->Grab();
        }
    }

    /**
     * 获取调用策略
     * @return type
     */
    public function getStrategy() {
        $query = "select strategy from search_strategy where strategy_id = $this->strategy_id limit 1";
        $strategy_name = $this->mysql_obj->fetch_assoc_one($query);
        $strategy_name = $strategy_name['strategy'];
        return "Model\\SpiderStrategy\\" . $strategy_name . "Strategy";
    }

}
