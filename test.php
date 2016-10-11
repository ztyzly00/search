<?php

require_once 'vendor/autoload.php';

use Goutte\Client;

$client = new Client();
$crawler = $client->request('GET', "http://www.dmoz.org/Computers/Programming/Languages/Python/Books/");

$nodes = $crawler->filter("#site-list-content > .site-item > .title-and-desc")->getChildren();

foreach($nodes as $node){
    print_r($node->html());
}
