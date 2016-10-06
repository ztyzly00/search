<?php

namespace Model\Utils;

class WebUtils {

    public static function toUtf8($str) {
        $encode = mb_detect_encoding($str, array('ASCII', 'UTF-8', 'GB2312', 'GBK', 'BIG5'));
        if ($encode != "UTF-8") {
            $str = mb_convert_encoding($str, "UTF-8", $encode);
        }
        return $str;
    }

}
