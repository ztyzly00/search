<?php

namespace Model\SpiderStrategy;

/**
 * 腾讯网抓取策略
 */
class SouHuStrategy extends Strategy {

    public static function getPContent($crawler) {
        $p_content = '';
        if ($crawler->filter('#contentText')->getNode(0)) {
            $p_content = $crawler->filter('#contentText')->html();
            if ($p_content) {
                return $p_content;
            }
        }

        if ($crawler->filter('#sohu_content')->getNode(0)) {
            $p_content = $crawler->filter('#sohu_content')->html();
            if ($p_content) {
                return $p_content;
            }
        }
        return $p_content;
    }

    public static function getTitle($crawler) {
        $title = '';
        if ($crawler->filter('.content-box > h1')->getNode(0)) {
            $title = $crawler->filter('.content-box > h1')->html();
            if ($title) {
                return $title;
            }
        }

        if ($crawler->filter('.article_area > h1')->getNode(0)) {
            $title = $crawler->filter('.article_area > h1')->html();
            if ($title) {
                return $title;
            }
        }
        return $title;
    }

}
