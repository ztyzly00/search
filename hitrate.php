<?php

require_once 'autoload.php';

use Core\MySql\Mysql_Model\XmMysqlObj;
use Core\Redis\RedisFactory;

/**
 * 计算新闻获取的命中率
 */
$mysql_obj = XmMysqlObj::getInstance();
$redis_obj = RedisFactory::createRedisInstance();
$xm_redis_obj = RedisFactory::createXmRedisInstance();

$query = "select contentcount from search_count limit 1";
$content_count = $mysql_obj->fetch_assoc_one($query);
$content_count = $content_count['contentcount'];

$query = "select count(*) from search_strategy";
$strategy_count = $mysql_obj->fetch_assoc_one($query);
$strategy_count = $strategy_count['count(*)'];

$undo_count = 0;
for ($i = 1; $i <= $strategy_count - 1; $i++) {
    $undo_count+=$xm_redis_obj->sCard('spider_href_set_' . $i);
}
$all_count = $redis_obj->sCard('unique_href_set');
$do_count = $all_count - $undo_count;


$hitrate = $content_count / $do_count;

print_r($hitrate . "\n");
print_r($content_count . "\n");
print_r($do_count . "\n");
print_r($hitrate . "\n");
