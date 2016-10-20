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
 *      初期磁盘压力过大，大量的小文件块读写，机械硬盘iops跟不上(带宽只能卡在60m左右徘徊) (已解决，400mbps带宽已完全占满)
 *      初期磁盘压力过大造成php等待mysql响应，造成cpu浪费在mysql网络阻塞上 (已解决)
 *      后期update变多，insert变少，逐渐演变成抓取的带宽瓶颈，但是同样会造成cpu的网络阻塞（已解决）
 *      策略的过于复杂将直接影响cpu的效率(精简各站点Strategy文件的策略,最好先人为判断)
 *      当多策略同时运行时，大量的redis的存储于计算会导致redis的cpu到顶（redis是单进程应用），然后导致php等待redis，php负载也相应变高
 *          ->是搜狐的站点让redis负载变高，将搜狐的策略缩小即可（为何搜狐这么屌？）
 *      当增大策略的规模时，redis的内存不够用，造成swap，性能急剧下降
 *          ->还是需要缩小策略的范围。
 *          ->使用另外一台服务器redis存储，redis空间缓解        
 * 
 *          ->减小mysql所需内存，将大部分内存留给redis.(mysql的磁盘交互在大量抓取的情况下无法避免，高性能的去重优先交给redis)
 *              ->还是磁盘压力过大，只能取消记录抓取记录了。只记录内容，可以少很多次mysql交互
 *          
 *          ->提高抓取策略算法，下一步打算从a标签的特性入手（从新闻网站的连接特性）
 *              ->超链接的字数
 * 
 * 建议：
 *      针对初期磁盘压力过大，很大一部分原因是热度优先所搜导致硬盘读取，
 *           ->若热度优先搜索用redis代替则可以让抓取瓶颈变为带宽，但是redis没有想到好的解决方案
 *          (已解决,利用Redis set去重href，list来维护待抓取href，但不是热度优先搜索，变成广度优先了
 *           但是同样能抓遍全站数据，下一步需要考虑的是Redis状态码的回退问题，不过带宽顺利占满400m，io没有压力)
 * 
 *          【遇到新问题，在中后期，磁盘io又卡住了,在中后期的时候，mysql表的大小超过了innodb_buffer_pool_size（定为20G），将对磁盘做读取操作
 *           机械硬盘的机械特性在同时读取和写入时造成io严重堵塞。（解决办法为更换SSD硬盘或者是其他nosql数据库,无需维护索引的数据库。）
 *           目测解决还需一些类似于mongodb的文档数据库存储网页数据】
 *               ->【削减mysql所需数据量，让mysql的表数据小于innodb_buffer_pool_size的大小,大量字符串用mongodb存储】
 *               ->【mongodb建立索引后同样很慢，磁盘瓶颈主要体现在整个document内容的插入，应该舍弃部分内容,提取精华内容插入】
 *               ->【解决办法，扩大innodb_buffer_pool_size（暂时性），无索引的关系表分离，防止内存与硬盘做太多io交互】（已解决）
 *            
 * 
 *      若可以采用python抓取更好，应该使用epoll维持响应，Goutte库会造成响应阻塞（需要用多进程弥补）
 *      数据库建议采用Mongodb或者hbase，用mysql维持关系性，采用redis维持href去重列表（不依靠mysql的唯一键） (已解决,Mongodb暂时不需要)
 *      优先优化mysql，尽量减少表索引数量，但要保证select的迅速响应。（目前time索引没有必要，但暂时保留） 
 *           ->  利用了Redis删除了三个索引，只留一个href唯一索引)（已解决）
 *           ->  分表，href唯一索引也删除，用redis来保证唯一性
 * 
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
     * 过滤字符串数组(抛弃过滤)
     * @var type 
     */
    private $filter_array_abandon;
    private $filter_abandon_flag;

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
    private $xm_redis_obj;

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
        $this->xm_redis_obj = RedisFactory::createXmRedisInstance();
        $this->client = new Client();
    }

    /**
     * 获取批量插入的query语句,用于href的插入
     * @param type $nodes
     * @param type $from_href
     * @return string
     */
    public function insertHref($crawler) {
        $crawler_a = $crawler->filter('a');
        $nodes = $crawler_a->getNodes();

        for ($i = 0; $i < count($nodes); $i++) {
            $node = $nodes[$i];
            $href = $node->getAttribute('href');

            /* 判断合法性并进行href的过滤整合 */
            if (!$this->isHrefLegal($href)) {
                $query = "insert into search_rubbish (`href`,`strategy_id`) values ('$href',$this->strategy_id)";
                $this->mysql_obj->exec_query($query);
                continue;
            } else {
                
            }

            $href = addslashes($href);
            if ($this->redis_obj->sAdd('unique_href_set', $href)) {
                $this->xm_redis_obj->sAdd('spider_href_set_' . $this->strategy_id, $href);
            }
        }
    }

    /**
     * 判断连接是否合法，合法返回true，否则为false(利用引用顺带过滤)
     * 高频运算（有待算法优化）(一秒大概4w左右的频率)
     * @param type $href
     * @return boolean
     */
    public function isHrefLegal(&$href) {

        /* 关键字符包含过滤 */
        if (strpos($href, "#") || strpos($href, "?")) {
            return 0;
        }

        if (strpos($href, "http") === FALSE) {

            /* 判断是否可能是简洁路径,若是php和asp等动态不在考虑内，防止对方服务器爆炸  */
            if (strpos($href, "htm") !== FALSE) {
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
            $query = "select string from search_filter "
                    . "where strategy_id=$this->strategy_id";
            $this->filter_array = $this->mysql_obj->fetch_assoc($query);
        }
        $filter_array = $this->filter_array;
        for ($i = 0; $i < count($filter_array); $i++) {
            if (strpos($href, $filter_array[$i]['string']) !== FALSE) {
                $flag = true;
            }
        }

        /* 需要全局flag判定是否数据库中是否有值，要不会每次都读取数据库 */
        $flag_abandon = true;
        if (!$this->filter_array_abandon && $this->filter_abandon_flag != "close") {
            $query = "select string from search_filter_abandon "
                    . "where strategy_id=$this->strategy_id";
            $this->filter_array_abandon = $this->mysql_obj->fetch_assoc($query);
            if (!count($this->filter_array_abandon)) {
                $this->filter_abandon_flag = "close";
            }
        }
        $filter_array_abandon = $this->filter_array_abandon;
        for ($i = 0; $i < count($filter_array_abandon); $i++) {
            if (strpos($href, $filter_array_abandon[$i]['string']) !== FALSE) {
                $flag_abandon = false;
            }
        }

        if ($flag && $flag_abandon) {
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
        $curr_href = $this->xm_redis_obj->sPop('spider_href_set_' . $this->strategy_id);

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
    public function recordInfo($crawler) {

        /* 分类添加新闻网页的新闻内容,title等内容 */
        $title = call_user_func(array($this->getStrategy(), 'getTitle'), $crawler);
        if ($title) {
            $p_content = call_user_func(array($this->getStrategy(), 'getPContent'), $crawler);

            /* 字符处理 */
            $p_content = WebUtils::toUtf8($p_content);
            $p_content = addslashes($p_content);
            $title = WebUtils::toUtf8($title);
            $title = addslashes($title);
        }

        if ($title && $p_content) {
            /* 记录到内容表中 */
            $query = "insert into search_content (`title`,`href`,`pcontent`,`strategy_id`) values "
                    . "('$title','$this->curr_href','$p_content',$this->strategy_id)";
            $this->mysql_obj->exec_query($query);

            $exec_query = "update search_count set contentcount=contentcount+1";
            $this->mysql_obj->exec_query($exec_query);
        } else {
            /* 调试性能段 */
//            $query = "insert into search_rubbish (`href`,`strategy_id`) values ('$this->curr_href',$this->strategy_id)";
//            $this->mysql_obj->exec_query($query);
        }
    }

    public function grabHref($curr_href) {

        $crawler = $this->client->request('GET', $curr_href);

        $this->recordInfo($crawler);

        /* 添加网页中的所有连接,用于下一步的抓取 */
        $this->insertHref($crawler);
    }

    public function Grab() {

        /* 大量操作数据库前,获取当前抓取的连接 */
        $curr_href = $this->getCurrHref();

        /* 操作失败回退 */
        register_shutdown_function(function() {
            $this->xm_redis_obj->sAdd('spider_href_set_' . $this->strategy_id, $this->curr_href);
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
