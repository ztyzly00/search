<?php

require_once 'autoload.php';
require_once 'vendor/autoload.php';

use Model\HrefSearcher;
use Core\MySql\Mysql_Model\XmMysqlObj;
use Core\Redis\RedisFactory;

/* 总pid的数量 */
$total_pid_num = 400;

$strategy_array = getStrategyArray();
$origin_pid_num = intval($total_pid_num / count($strategy_array));

$redis_obj = RedisFactory::createRedisInstance(1);

for ($i = 0; $i < count($strategy_array); $i++) {
    $strategy_id = $strategy_array[$i];
    $redis_obj->set('spider_strategy_pid_num_' . $strategy_id, $origin_pid_num);

    $pids_array[$i] = pcntl_fork();
    switch ($pids_array[$i]) {
        case -1:
            echo "fork error:{$pids_array[$i]}\r\n";
            exit;
        case 0:
            strategy_start($strategy_id);
            exit;
        default:
            break;
    }
}

/* 后续多进程的维护 */
$spider_href_num = array();
while (true) {

    if (count($strategy_array) == 0) {
        exit;
    }

    sleep(5);

    $xm_redis_obj = RedisFactory::createXmRedisInstance(1);

    for ($i = 0; $i < count($strategy_array); $i++) {

        $strategy_id = $strategy_array[$i];
        $curr_href_num = $xm_redis_obj
                ->sCard('spider_href_set_' . $strategy_id);

        if (isset($spider_href_num[$strategy_id])) {
            $prev_href_num = $spider_href_num[$strategy_id];

            /* 待抓取队列已基本枯竭 */
            if ($curr_href_num == 0 &&
                    $prev_href_num == 0) {

                /*
                 * 修改维护进程数 
                 * 废除老策略进程
                 * 将其他进程数增加（保持总进程数不变）
                 */
                $redis_obj->set('spider_strategy_pid_num_' . $strategy_id, '0');
                $xm_redis_obj->del('spider_href_set_' . $strategy_id);

                for ($i = 0; $i < count($strategy_array); $i++) {
                    if ($strategy_array[$i] == $strategy_id) {
                        unset($strategy_array[$i]);
                        $strategy_array = array_values($strategy_array);
                        break;
                    }
                }

                $pid_num = intval($total_pid_num / count($strategy_array));
                foreach ($strategy_array as $strategy_id) {
                    $redis_obj->set('spider_strategy_pid_num_' . $strategy_id, $pid_num);
                }
            }
        }

        $spider_href_num[$strategy_id] = $curr_href_num;
    }
}

/* 回收子进程 */
foreach ($pids_array as $i => $pid) {
    if ($pid) {
        pcntl_waitpid($pid, $status);
    }
}

/**
 * 根据策略值开始并行抓取新闻
 * @param type $strategy_id
 */
function strategy_start($strategy_id) {

    $redis_obj = RedisFactory::createRedisInstance(1);

    while (true) {

        $pid = pcntl_fork();
        $pids[$pid] = $pid;

        switch ($pid) {
            case -1:
                echo "fork error:{$pid}\r\n";
                exit;
            case 0:
                $href_searcher = new HrefSearcher($strategy_id);
                $href_searcher->startGrab();
                exit;
            default:
                $pid_num = $redis_obj->get('spider_strategy_pid_num_' . $strategy_id);
                if ($pid_num == 0) {
                    foreach ($pids as $pid) {
                        if ($pid) {
                            $status = 0;
                            pcntl_waitpid($pid, $status);
                        }
                    }
                    exit;
                } else if (count($pids) > $pid_num) {
                    $status = 0;
                    $pid = pcntl_waitpid(-1, $status);
                    unset($pids[$pid]);
                }
                break;
        }
    }
}

function getStrategyCount() {
    $mysql_obj = XmMysqlObj::getInstance(1);

    $query = "select count(*) from search_strategy";
    $strategy_count = $mysql_obj->fetch_assoc_one($query);
    $strategy_count = $strategy_count['count(*)'];

    return $strategy_count;
}

function getStrategyArray() {
    $strategy_count = getStrategyCount();

    $strategy_array = array();
    for ($i = 1; $i <= $strategy_count - 1; $i++) {
        $strategy_array[] = $i;
    }

    return $strategy_array;
}
