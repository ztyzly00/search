<?php

use Ovr\PHPReflection\Reflector;

include_once __DIR__ . '/vendor/autoload.php';

$reflector = new Reflector(Reflector::manuallyFactory());
$reflection = $reflector->getFunction('gettype');
var_dump($reflection);
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getNumberOfRequiredParameters());
var_dump($reflection->run(array(1)));
