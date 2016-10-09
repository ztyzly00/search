<?php

require_once 'vendor/autoload.php';

use Goutte\Client;

$client = new Client();
$crawler = $client->request('GET', "http://news.sohu.com/20161004/n469603932.shtml");

$title = $crawler->filter(".content-box > h1")->html();

//print_r($crawler->getContent());
print_r($title);
