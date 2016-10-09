<?php

namespace Model\SpiderStrategy;

/**
 * 腾讯网抓取策略
 */
class TengXunStrategy extends Strategy {

    public static function getPContent($crawler) {
        $p_content = '';
        if ($crawler->filter('#Cnt-Main-Article-QQ')->getNode(0)) {
            $p_content = $crawler->filter('#Cnt-Main-Article-QQ')->html();
        }
        return $p_content;
    }

    public static function getTitle($crawler) {
        $title = '';
        if ($crawler->filter('.hd > h1')->getNode(0)) {
            $title = $crawler->filter('.hd > h1')->html();
        }
        return $title;
    }

}
