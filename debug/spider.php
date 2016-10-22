<?php

require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Model\HrefSearcher;

$strategy_id = $argv[1];
if (!(isset($argv[1]))) {
    echo "没写参数" . "\n";
    exit();
}

$href_searcher = new HrefSearcher($strategy_id);
$href_searcher->startGrab();