<?php

set_time_limit(0);
ignore_user_abort(true);

/* 线程数 */
$pid_num = 50;

$strategy_id = $argv[1];

if (!(isset($argv[1]))) {
    echo "没写参数，参数值为策略id" . "\n";
    exit();
}

while (1) {
    $q = exec('ps -ef|grep "spider.php ' . $strategy_id . '"|wc|cut -c 5-10');
    $q = trim($q);
    if ($q < $pid_num) {
        $loop_count = $pid_num - $q;
        for ($i = 0; $i < $loop_count; $i++) {
            exec("php " . __DIR__ . "/spider.php " . $strategy_id . " > /dev/null &");
        }
    }

    sleep(1);
}


