<?php

namespace Core\Redis;

class RedisFactory {

    private static $redis_instance;

    public static function createRedisInstance() {
        if (self::$redis_instance == NULL) {
            self::$redis_instance = new \redis();
            self::$redis_instance->connect('127.0.0.1', 6379);
        }
        return self::$redis_instance;
    }

}
