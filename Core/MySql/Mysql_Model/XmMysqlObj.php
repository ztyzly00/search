<?php

namespace Core\MySql\Mysql_Model;

use Core\MySql\Mysql_Interface;
use Core\SqlLink;

class XmMysqlObj implements Mysql_Interface\iMySqlObj {

    private static $_instance;
    private $link;

    public function __construct($opt = 0) {
        $dataBaseInstance = SqlLink\SqlLinkFactory::createXmDatabase($opt);
        $this->link = $dataBaseInstance->getDbLink();
    }

    /**
     * 获取本身对象的实例
     * @return type
     */
    public static function getInstance($opt = 0) {
        /* 单例模式 */
        if ($opt == 0) {
            if (self::$_instance == NULL) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }
        /* 非单例模式 */ else {
            $new_instance = new self(1);
            return $new_instance;
        }
    }

    public function exec_query($query) {
        mysqli_query($this->link, $query);
    }

    public function fetch_array($query) {
        $result = mysqli_query($this->link, $query);
        $returnString = array();
        while ($row = mysqli_fetch_array($result)) {
            $returnString[] = $row;
        }
        return $returnString;
    }

    public function fetch_assoc($query) {
        $result = mysqli_query($this->link, $query);
        $returnString = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $returnString[] = $row;
        }
        return $returnString;
    }

    public function fetch_row($query) {
        
    }

    public function num_rows($query) {
        $result = mysqli_query($this->link, $query);
        if ($result) {
            $row_nums = mysqli_num_rows($result);
            return $row_nums;
        } else {
            return 0;
        }
    }

    public function fetch_array_one($query) {
        $result = mysqli_query($this->link, $query);
        if ($result) {
            $row = mysqli_fetch_array($result);
            return $row;
        } else {
            return null;
        }
    }

    public function fetch_assoc_one($query) {
        $result = mysqli_query($this->link, $query);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            return $row;
        } else {
            return null;
        }
    }

}
