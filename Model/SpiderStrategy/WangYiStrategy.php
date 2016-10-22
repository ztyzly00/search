<?php

namespace Model\SpiderStrategy;

/**
 * 腾讯网抓取策略
 */
class WangYiStrategy extends Strategy {

    public static $title_strategy_array = array('#epContentLeft > h1', '#h1title', '#endTitle', '.theTitle > h1', '.arcTitle > h1');
    public static $pcontent_strategy_array = array('#endText', '#text');

}
