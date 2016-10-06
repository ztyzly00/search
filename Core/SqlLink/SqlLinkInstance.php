<?php

namespace Core\SqlLink;

class SqlLinkInstance {

    /**
     * 数据库的用户名
     * @var type 
     */
    public $_MySql_UserName = null;

    /**
     * 数据库的密码
     * @var type 
     */
    public $_MySql_PassWord = null;

    /**
     * 数据库的server地址
     * @var type 
     */
    public $_MySql_Server = null;

    /**
     * 数据库中database的选择
     * @var type 
     */
    public $_MySql_DataBase = null;

    /**
     * 连接数据库句柄实例
     * @var type 
     */
    public $_MySql_Link = null;

    /**
     * 构造函数，设置为final防止继承调用
     */
    public final function __construct() {
        
    }

    public function getDataBase() {
        return $this->_MySql_DataBase;
    }

    public function setDataBase($dataBase) {
        $this->_MySql_DataBase = $dataBase;
    }

    public function getServer() {
        return $this->_MySql_Server;
    }

    public function setServer($server) {
        $this->_MySql_Server = $server;
    }

    public function getPassWord() {
        return $this->_MySql_PassWord;
    }

    public function setPassWord($password) {
        $this->_MySql_PassWord = $password;
    }

    public function getUserName() {
        return $this->_MySql_UserName;
    }

    public function setUserName($username) {
        $this->_MySql_UserName = $username;
    }

    public function getDbLink() {
        if (empty($this->_MySql_Link)) {
            $link = mysqli_connect($this->getServer(), $this->getUserName(), $this->getPassWord(), $this->getDataBase());
            mysqli_query($link, 'set names utf8;');
            mysqli_query($link, 'set character set \'utf8\'');
            $this->_MySql_Link = $link;
        }
        return $this->_MySql_Link;
    }

}
