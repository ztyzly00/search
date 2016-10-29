<?php

namespace Core\Config;

class mysqlconfig {

    static $config = [
        "localhost_scraper" => [
            "hostname" => "localhost",
            "username" => "webuser",
            "password" => "webuserpassword",
            "database" => "scraper",
        ],
        "20.3_scraper" => [
            "hostname" => "192.168.20.3",
            "username" => "webuser",
            "password" => "webuserpassword",
            "database" => "scraper",
        ],
        "20.2_scraper" => [
            "hostname" => "192.168.20.2",
            "username" => "webuser",
            "password" => "webuserpassword",
            "database" => "scraper",
        ]
    ];

}
