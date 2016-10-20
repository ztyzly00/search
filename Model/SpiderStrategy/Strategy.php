<?php

namespace Model\SpiderStrategy;

/**
 * 策略的过于复杂将直接影响cpu的效率
 */
abstract class Strategy {

    public static function getPContent($crawler) {
        $pcontent = '';
        $pcontent_strategy_array = static::$pcontent_strategy_array;
        foreach ($pcontent_strategy_array as $strategy) {
            if ($crawler->filter($strategy)->count()) {
                $pcontent = $crawler->filter($strategy)->html();
                if ($pcontent) {
                    return $pcontent;
                }
            }
        }
        return $pcontent;
    }

    public static function getTitle($crawler) {
        $title = '';
        $title_strategy_array = static::$title_strategy_array;
        foreach ($title_strategy_array as $strategy) {
            if ($crawler->filter($strategy)->count()) {
                $title = $crawler->filter($strategy)->html();
                if ($title) {
                    return $title;
                }
            }
        }
        return $title;
    }

}
