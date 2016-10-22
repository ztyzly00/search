<?php

namespace Model\SpiderStrategy;

/**
 * 腾讯网抓取策略
 */
class TengXunStrategy extends Strategy {

    public static $title_strategy_array = array('.hd > h1', '#ArticleTit');
    public static $pcontent_strategy_array = array('#Cnt-Main-Article-QQ', '#ArticleCnt');

}
