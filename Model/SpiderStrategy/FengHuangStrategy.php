<?php

namespace Model\SpiderStrategy;

class FengHuangStrategy extends Strategy {

    public static function getPContent($crawler) {
        $p_content = '';
        if ($crawler->filter('#main_content')->count()) {
            $p_content = $crawler->filter('#main_content')->html();
            if ($p_content) {
                return $p_content;
            }
        }

        if ($crawler->filter('#artical_real')->count()) {
            $p_content = $crawler->filter('#artical_real')->html();
            if ($p_content) {
                return $p_content;
            }
        }

        return $p_content;
    }

    public static function getTitle($crawler) {
        $title = '';
        if ($crawler->filter('#artical_topic')->count()) {
            $title = $crawler->filter('#artical_topic')->html();
        }
        return $title;
    }

}
