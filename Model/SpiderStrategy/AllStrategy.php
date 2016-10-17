<?php

namespace Model\SpiderStrategy;

/**
 * 新华网抓取策略
 */
class AllStrategy extends Strategy {

    public static function getPContent($crawler) {
        $p_content = '';
        if ($crawler->filter('#artibody')->getNode(0)) {
            $p_content = $crawler->filter('#artibody')->html();
            if ($p_content) {
                return $p_content;
            }
        }

        return $p_content;
    }

    public static function getTitle($crawler) {
        $title = '';
        if ($crawler->filter('#artibodyTitle')->count()) {
            $title = $crawler->filter('#artibodyTitle')->html();
            if ($title) {
                return $title;
            }
        }

        if ($crawler->filter('#main_title')->count()) {
            $title = $crawler->filter('#main_title')->html();
            if ($title) {
                return $title;
            }
        }

        if ($crawler->filter('.article-header h1')->count()) {
            $title = $crawler->filter('.article-header h1')->html();
            if ($title) {
                return $title;
            }
        }

        return $title;
    }

}
