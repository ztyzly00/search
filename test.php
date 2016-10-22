<?php

require_once 'vendor/autoload.php';
require_once 'autoload.php';

use Goutte\Client;
use Core\Redis\RedisFactory;
use Core\MySql\Mysql_Model\XmMysqlObj;
use Symfony\Component\DomCrawler\Crawler;

$client = new Client();
$crawler = $client->request('GET', "http://news.ifeng.com/world/detail_2009_09/07/1179502_0.shtml");
$html = $crawler->getContent();

print_r($html);

//
//$curr_crawler = new Crawler($html);
//
//$nodes = $curr_crawler->filter(".article")->html();
//
//print_r($nodes);
//$redis_obj = RedisFactory::createRedisInstance();
//
//for ($i = 0; $i < 10000; $i++) {
//    $redis_obj->set("zty", "zly");
//}
//$query = "insert into test (`href`) values ('http://www.baidu.com');";
// . "insert into test (`href`) values ('http://www.souhu.com')";
//$mysql_obj = XmMysqlObj::getInstance();
//$mysql_obj->exec_query($query);

