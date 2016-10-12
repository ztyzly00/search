<?php

require_once 'vendor/autoload.php';
require_once 'autoload.php';

use Goutte\Client;
use Core\Redis\RedisFactory;

//$client = new Client();
//$crawler = $client->request('GET', "http://www.dmoz.org/Computers/Programming/Languages/Python/Books/");
//
//$nodes = $crawler->filter("#site-list-content > .site-item > .title-and-desc")->getChildren();
//
//foreach($nodes as $node){
//    print_r($node->html());
//}

$redis_obj = RedisFactory::createRedisInstance();

for ($i = 0; $i < 10000; $i++) {
    $redis_obj->set("zty", "zly");
}
