<?php

namespace Core\MySql;

use Core\Config\mysqlconfig;

/**
 * 数据库操作抽象类
 */
class DBFactory {

    private static $db_pool = [];

    /**
     * 获取db操作类实例
     * @param type $db_name db名称
     * @param type $opt 0代表单例模式，1代表非单例模式
     */
    public static function getDb($db_name, $opt = 0) {
        if ($opt == 0) {
            if (array_key_exists($db_name, self::$db_pool)) {
                return self::$db_pool[$db_name];
            } else {
                self::$db_pool[$db_name] = new DBObj(mysqlconfig::$config[$db_name]);
                return self::$db_pool[$db_name];
            }
        } else if ($opt == 1) {
            return new DBObj(mysqlconfig::$config[$db_name]);
        }
    }

}
