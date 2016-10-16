<?php

namespace Model\SpiderStrategy;

/**
 * 策略的过于复杂将直接影响cpu的效率
 */
abstract class Strategy {

    abstract public static function getPContent($crawler);

    abstract public static function getTitle($crawler);
}
