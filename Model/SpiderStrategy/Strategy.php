<?php

namespace Model\SpiderStrategy;

abstract class Strategy {

    abstract public static function getPContent($crawler);

    abstract public static function getTitle($crawler);
}
