<?php

namespace Core\MySql;

class DBObj {

    private $link;

    /**
     * 可以采取惰性连接，这里为了方便，不弄惰性加载
     * @param type $database
     */
    public function __construct($database) {
        $this->link = mysqli_connect($database['hostname'], $database['username']
                , $database['password'], $database['database']);
        mysqli_query($this->link, 'set names utf8;');
        mysqli_query($this->link, 'set character set \'utf8\'');
    }

    public function exec_query($query) {
        return mysqli_query($this->link, $query);
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

    public function get_link() {
        return $this->link;
    }

}
