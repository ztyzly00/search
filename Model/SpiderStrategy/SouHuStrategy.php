<?php

namespace Model\SpiderStrategy;

/**
 * 腾讯网抓取策略
 */
class SouHuStrategy extends Strategy {

    public static $title_strategy_array = array('.content-box > h1', '.article_area > h1', '#contentA > .left > h1', '#contentB > .left > h1');
    public static $pcontent_strategy_array = array('#contentText', '#sohu_content');

}
