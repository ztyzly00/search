<?php

namespace Model\SpiderStrategy;

/**
 * 新华网抓取策略
 */
class XinLangStrategy extends Strategy {

    public static $title_strategy_array = array('#artibodyTitle', '#main_title', '.article-header h1', '.f24 > font > h1');
    public static $pcontent_strategy_array = array('#artibody', '.f14');

}
