<?php

namespace Core\Redis;

class RedisFactory {

    private static $xm_redis_instance;
    private static $redis_instance;

    public static function createRedisInstance($opt = 0) {
        if ($opt == 0) {
            if (self::$redis_instance == NULL) {
                self::$redis_instance = new \redis();
                self::$redis_instance->connect('127.0.0.1', 6379);
            }
            return self::$redis_instance;
        } else if ($opt == 1) {
            $new_redis_instance = new \redis();
            $new_redis_instance->connect('127.0.0.1', 6379);
            return $new_redis_instance;
        }
        return self::$redis_instance;
    }

    public static function createXmRedisInstance($opt = 0) {
        if ($opt == 0) {
            if (self::$xm_redis_instance == NULL) {
                self::$xm_redis_instance = new \redis();
                self::$xm_redis_instance->connect('192.168.20.3', 6379);
            }
            return self::$xm_redis_instance;
        } else if ($opt == 1) {
            $new_redis_instance = new \redis();
            $new_redis_instance->connect('192.168.20.3', 6379);
            return $new_redis_instance;
        }
        return self::$redis_instance;
    }

}
