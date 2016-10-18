<?php

require_once 'vendor/autoload.php';
require_once 'autoload.php';

use Core\MySql\Mysql_Model\XmMysqlObj;
use Core\Redis\RedisFactory;

$mysql_obj = XmMysqlObj::getInstance();
$redis_obj = RedisFactory::createRedisInstance();

$redis_obj->flushall();
$mysql_obj->exec_query("delete from search_href");
$mysql_obj->exec_query("delete from search_content");
$mysql_obj->exec_query("delete from search_rubbish");
$mysql_obj->exec_query("update search_count set hrefcount=0,contentcount=0");
