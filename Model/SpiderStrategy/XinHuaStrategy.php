<?php

namespace Model\SpiderStrategy;

/**
 * 新华网抓取策略
 */
class XinHuaStrategy extends Strategy {

    public static $title_strategy_array = array('#title', '#Title', '#news_title', '.tou > h1');
    public static $pcontent_strategy_array = array('#content', '.article', '#Content', '#news_content');

}
