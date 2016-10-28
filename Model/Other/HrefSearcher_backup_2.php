<?php

namespace Model;

use Core\MySql\Mysql_Model\XmMysqlObj;
use Core\Redis\RedisFactory;
use Model\Utils\WebUtils;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

/**
 * author：镇天宇
 * 
 * 介紹：
 *      高并发新闻内容爬虫
 *      适用于大型新闻网站的全站抓取
 * 
 * 机器要求：
 *      对内存，硬盘有严格要求，内存大于40g才可尝试。
 *      
 * 软件要求：
 *      需求redis和mysql数据库
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
     * 策略call方式缓存
     * @var type 
     */
    private $strategy_name;

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
    private $curl_handle;
    private $client;
    private $reg_shutdown_mutex;

    /**
     * 构造函数
     * @param type $strategy_id
     */
    public function __construct($strategy_id) {
        $this->strategy_id = $strategy_id;
        $this->mysql_obj = XmMysqlObj::getInstance();
        $this->redis_obj = RedisFactory::createRedisInstance();
        $this->xm_redis_obj = RedisFactory::createXmRedisInstance();
        $this->curl_init();
        $this->client = new Client();
    }

    public function startGrab() {
        $i = 0;
        while (true) {
            $pid_num = $this->redis_obj->
                    get('spider_strategy_pid_num_' . $this->strategy_id);
            $pid_num = 1;
            if ($pid_num) {
                $this->Grab();
                $i++;
                if ($i == 30) {
                    exit;
                }
                //exit;
            } else {
                exit;
            }
        }
    }

    public function Grab() {

        $curr_href = $this->getCurrHref();

        register_shutdown_function(function() {
            if (!$this->reg_shutdown_mutex) {
                $this->xm_redis_obj->
                        sAdd('spider_href_set_' . $this->strategy_id, $this->curr_href);
                $this->reg_shutdown_mutex = 1;
            }
        });

        $this->grabHref($curr_href);
    }

    public function curl_init() {
        $this->curl_handle = curl_init();
        curl_setopt($this->curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl_handle, CURLOPT_HEADER, 0);
        curl_setopt($this->curl_handle, CURLOPT_TIMEOUT_MS, 1000);
    }

    public function curl_get($href) {
        curl_setopt($this->curl_handle, CURLOPT_URL, $href);
        $html = curl_exec($this->curl_handle);
        $curl_errno = curl_errno($this->curl_handle);
        if ($curl_errno > 0) {
            $this->xm_redis_obj->
                    sAdd('spider_href_set_' . $this->strategy_id, $this->curr_href);
            return 'error';
        }
        return $html;
    }

    public function grabHref($curr_href) {

        $html = $this->curl_get($curr_href);
        if ($html == 'error') {
            return;
        }
        $crawler = new Crawler($html);
//        $crawler = $this->client->request('GET', $curr_href);

        $this->recordInfo($crawler);

        $this->insertHref($crawler);
    }

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
            $title = trim($title);
        }

        if ($title && $p_content) {
            /* 记录到内容表中 */
            $query = "insert into search_content (`title`,`href`,`pcontent`,`strategy_id`) values "
                    . "('$title','$this->curr_href','$p_content',$this->strategy_id)";
            $this->mysql_obj->exec_query($query);

            $exec_query = "update search_count set contentcount=contentcount+1";
            $this->mysql_obj->exec_query($exec_query);
        } else {
            //$query = "insert into search_rubbish (`href`,`strategy_id`) values ('$this->curr_href',$this->strategy_id)";
            //$this->mysql_obj->exec_query($query);
        }
    }

    /**
     * 批量插入待抓取的href
     * @param type $crawler
     */
    public function insertHref($crawler) {
        $crawler_a = $crawler->filter('a');
        $nodes = $crawler_a->getNodes();

        for ($i = 0; $i < count($nodes); $i++) {
            $node = $nodes[$i];
            $href = $node->getAttribute('href');

            /* 判断合法性并进行href的过滤整合 */
            if (!$this->isHrefLegal($href)) {
                continue;
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
     * 当前抓取href获取策略
     * @return type
     */
    public function getCurrHref() {

        $curr_href = $this->xm_redis_obj->sPop('spider_href_set_' . $this->strategy_id);

        if (!$curr_href) {
            $href = $this->mysql_obj->fetch_assoc_one("select href from search_orgin where strategy_id=$this->strategy_id limit 1");
            $curr_href = $href['href'];
        }

        $curr_href = addslashes($curr_href);

        $this->curr_href = $curr_href;

        return $curr_href;
    }

    /**
     * 获取调用策略
     * @return type
     */
    public function getStrategy() {

        if (!$this->strategy_name) {
            $query = "select strategy from search_strategy where strategy_id = $this->strategy_id limit 1";
            $strategy_name = $this->mysql_obj->fetch_assoc_one($query);
            $this->strategy_name = $strategy_name['strategy'];
        }

        return "Model\\SpiderStrategy\\" . $this->strategy_name . "Strategy";
    }

}
