<?php

require_once 'vendor/autoload.php';
require_once 'autoload.php';

use Goutte\Client;
use Core\Redis\RedisFactory;

$client = new Client();
$crawler = $client->request('GET', "http://news.xinhuanet.com/city/2016-10/14/c_129322108.htm");

$nodes = $crawler->filter(".article")->html();

print_r($nodes);

//$redis_obj = RedisFactory::createRedisInstance();
//
//for ($i = 0; $i < 10000; $i++) {
//    $redis_obj->set("zty", "zly");
//}
