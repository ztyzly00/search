<?php

namespace Model\SpiderStrategy;

/**
 * 新华网抓取策略
 */
class XinHuaStrategy extends Strategy {

    public static function getPContent($crawler) {
        $p_content = '';
        if ($crawler->filter('#content')->getNode(0)) {
            $p_content = $crawler->filter('#content')->html();
        }
        return $p_content;
    }

    public static function getTitle($crawler) {
        $title = '';
        if ($crawler->filter('#title')->getNode(0)) {
            $title = $crawler->filter('#title')->html();
        }
        print_r($title);
        return $title;
    }

}
