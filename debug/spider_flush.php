<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../autoload.php';

use Core\MySql\Mysql_Model\XmMysqlObj;
use Core\Redis\RedisFactory;

$mysql_obj = XmMysqlObj::getInstance();
$redis_obj = RedisFactory::createRedisInstance();
$xm_redis_obj = RedisFactory::createXmRedisInstance();

$redis_obj->flushall();
$query = "select count(*) from search_strategy";
$count = $mysql_obj->fetch_assoc_one($query);
$count = $count['count(*)'];
for ($i = 1; $i <= $count; $i++) {
    $xm_redis_obj->del('spider_href_set_' . $i);
}

$mysql_obj->exec_query("delete from search_href");
$mysql_obj->exec_query("delete from search_content");
$mysql_obj->exec_query("delete from search_rubbish");
$mysql_obj->exec_query("update search_count set hrefcount=0,contentcount=0");
