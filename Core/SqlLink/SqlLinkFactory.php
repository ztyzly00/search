<?php

namespace Core\SqlLink;

use Core\SqlLink\SqlLinkInstance;

class SqlLinkFactory {

    public static $tp_instance;
    public static $common_instance;
    public static $xm_instance;

    public static function createNtjoyDatabase($opt = 0) {
        if ($opt == 0) {
            if (self::$tp_instance == NULL) {
                self::$tp_instance = new SqlLinkInstance();
                self::$tp_instance->setServer('192.168.20.20');
                self::$tp_instance->setUserName('webuser');
                self::$tp_instance->setPassWord('webuserpassword');
                self::$tp_instance->setDataBase('com_ntjoy_www');
            }
            return self::$tp_instance;
        } else {
            $new_instance = new SqlLinkInstance();
            $new_instance->setServer('192.168.20.20');
            $new_instance->setUserName('webuser');
            $new_instance->setPassWord('webuserpassword');
            $new_instance->setDataBase('com_ntjoy_www');
            return $new_instance;
        }
    }

    public static function createXmDatabase($opt = 0) {
        if ($opt == 0) {
            if (self::$xm_instance == NULL) {
                self::$xm_instance = new SqlLinkInstance();
                self::$xm_instance->setServer('192.168.20.2');
                self::$xm_instance->setUserName('webuser');
                self::$xm_instance->setPassWord('webuserpassword');
                self::$xm_instance->setDataBase('scraper');
            }
            return self::$xm_instance;
        } else {
            $new_instance = new SqlLinkInstance();
            $new_instance->setServer('192.168.20.2');
            $new_instance->setUserName('webuser');
            $new_instance->setPassWord('webuserpassword');
            $new_instance->setDataBase('scraper');
            return $new_instance;
        }
    }

    public static function createCommonData($server, $username, $password, $database) {
        self::$common_instance = new SqlLinkInstance();
        self::$common_instance->setServer($server);
        self::$common_instance->setUserName($username);
        self::$common_instance->setPassWord($password);
        self::$common_instance->setDataBase($database);
        return self::$common_instance;
    }

}
