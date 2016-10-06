<?php

function classLoader($class) {
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    //$file = __DIR__ . DIRECTORY_SEPARATOR ."你自己的目录". DIRECTORY_SEPARATOR . $path . '.php';
    $file = __DIR__ . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . $path . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
}

spl_autoload_register('classLoader');
