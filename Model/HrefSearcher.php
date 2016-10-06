<?php

namespace Model;

use Core\MySql\Mysql_Model\XmMysqlObj;
use Model\Utils\WebUtils;
use Goutte\Client;

/**
 * href连接爬虫，适合并发大量拉取带有过滤条件href
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

            if (!$query) {
                $query = "insert into search_href "
                        . "(`href`,`from_href`,`strategy_id`,`time`,`num`,`status`) "
                        . "values "
                        . "('$href','$from_href','$this->strategy_id',$time,1,0)";
            } else {
                $query.=",('$href','$from_href','$this->strategy_id',$time,1,0)";
            }
        }

        /* 重复数据将num+1，优先值也加1 */
        $query.=" ON duplicate KEY UPDATE num=num+1,priority=priority+1";
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
            return 1;
        }

        return 0;
    }

    /**
     * 当前抓取href获取策略 ， 采用热度优先搜索(根据连接的重复性判断优先级)
     * @return type
     */
    public function getCurrHref() {

        /* 获取当前需要抓取的href */
        $query = "select * from search_href "
                . "where status=0 and strategy_id=$this->strategy_id "
                . "order by priority desc limit 1";
        $curr_href = $this->mysql_obj->fetch_assoc_one($query);

        if (!count($curr_href)) {
            /* 取策略的最初href */
            $href = $this->mysql_obj->fetch_assoc_one("select href from search_orgin where strategy_id=$this->strategy_id limit 1");
            $curr_href = $href['href'];
        } else {
            $curr_href = $curr_href['href'];
        }
        /* 赋值给属性，用于回退操作 */
        $this->curr_href = $curr_href;

        return $curr_href;
    }

    /**
     * 标记已经抓取过href
     * @param type $curr_href
     */
    public function markHref($curr_href, $status) {
        /* 标识抓取过的不再抓取 */
        $query = "update search_href set status=$status where href='$curr_href'";
        $this->mysql_obj->exec_query($query);
        return $curr_href;
    }

    public function grabHref($curr_href) {

        $crawler = $this->client->request('GET', $curr_href);

        /* 添加当前网页的所有抓取内容 */
        $do = addslashes($crawler->getContent());
        $do = WebUtils::toUtf8($do);
        $query = "update search_href set content='$do' where href='$curr_href'";
        $this->mysql_obj->exec_query($query);

        /* 添加新闻网页的新闻内容 */


        /* 添加网页中的所有连接 */
        $nodes = $crawler->filter('a')->getNodes();
        $query = $this->getInsertQuery($nodes, $curr_href);
        $this->mysql_obj->exec_query($query);
    }

    public function Grab() {

        /* 大量操作数据库前 */
        $curr_href = $this->getCurrHref();

        /* 预先标记已经抓取，防止并发的重复性 */
        $this->markHref($curr_href, 1);

        /* 操作失败回退 */
        register_shutdown_function(function() {
            $this->markHref($this->curr_href, 0);
            $query = "update search_href set priority=0 where href='$this->curr_href'";
            $this->mysql_obj->exec_query($query);
        });

        /* 大量数据库操作 大概1秒左右响应（包括爬虫的href抓取） */
        $this->grabHref($curr_href);
    }

    public function startGrab() {

        while (true) {
            $this->Grab();
        }
    }

}
