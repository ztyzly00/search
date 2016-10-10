<?php

namespace Model\SpiderStrategy;

/**
 * 搜狐新闻抓取策略
 */
class SouHuStrategy extends Strategy {

    public static function getPContent($crawler) {
        $p_content = '';
        if ($crawler->filter('#contentText')->getNode(0)) {
            $p_content = $crawler->filter('#contentText')->html();
        }
        return $p_content;
    }

    public static function getTitle($crawler) {
        $title = '';
        if ($crawler->filter('.content-box > h1')->getNode(0)) {
            $title = $crawler->filter('.content-box > h1')->html();
        }
        return $title;
    }

}
