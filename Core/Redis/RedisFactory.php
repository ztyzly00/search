<?php

namespace Core\Redis;

use Core\Config\redisconfig;

class RedisFactory {

    private static $db_pool = [];

    public static function getDb($db_name, $opt) {
        if ($opt == 0) {
            if (array_key_exists($db_name, self::$db_pool)) {
                return self::$db_pool[$db_name];
            } else {
                $redis = new \redis();
                $redis->connect(redisconfig::$config[$db_name]['hostname']
                        , redisconfig::$config[$db_name]['port']);
                self::$db_pool[$db_name] = $redis;
                return self::$db_pool[$db_name];
            }
        } else if ($opt == 1) {
            $redis = new \redis();
            $redis->connect(redisconfig::$config[$db_name]['hostname']
                    , redisconfig::$config[$db_name]['port']);
            return $redis;
        }
    }

    public static function createRedisInstance($opt = 0) {
        return self::getDb('localhost', $opt);
    }

    public static function createXmRedisInstance($opt = 0) {
        return self::getDb('localhost', $opt);
    }

}
