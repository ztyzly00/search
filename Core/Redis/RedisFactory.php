<?php

namespace Core\Redis;

class RedisFactory {

    private static $xm_redis_instance;
    private static $redis_instance;

    public static function createRedisInstance() {
        if (self::$redis_instance == NULL) {
            self::$redis_instance = new \redis();
            self::$redis_instance->connect('127.0.0.1', 6379);
        }
        return self::$redis_instance;
    }

    public static function createXmRedisInstance() {
        if (self::$xm_redis_instance == NULL) {
            self::$xm_redis_instance = new \redis();
            self::$xm_redis_instance->connect('192.168.20.3', 6379);
        }
        return self::$xm_redis_instance;
    }

}
