<?php

namespace Model\SpiderStrategy;

/**
 * 腾讯网抓取策略
 */
class WangYiStrategy extends Strategy {

    public static function getPContent($crawler) {
        $p_content = '';
        if ($crawler->filter('#endText')->getNode(0)) {
            $p_content = $crawler->filter('#endText')->html();
        }
        return $p_content;
    }

    public static function getTitle($crawler) {
        $title = '';
        if ($crawler->filter('#epContentLeft > h1')->getNode(0)) {
            $title = $crawler->filter('#epContentLeft > h1')->html();
        }
        return $title;
    }

}
