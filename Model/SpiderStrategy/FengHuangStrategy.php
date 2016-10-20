<?php

namespace Model\SpiderStrategy;

/**
 * 凤凰网抓取策略
 */
class FengHuangStrategy extends Strategy {

    public static $title_strategy_array = array('#artical_topic');
    public static $pcontent_strategy_array = array('#main_content', '#artical_real');

}
