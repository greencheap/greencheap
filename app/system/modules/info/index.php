<?php

use GreenCheap\Info\InfoHelper;

return [
    "name" => "system/info",

    "main" => function ($app) {
        $app["info"] = function () {
            return new InfoHelper();
        };
    },

    "autoload" => [
        "GreenCheap\\Info\\" => "src",
    ],

    "routes" => [
        "/system/info" => [
            "name" => "@system/info",
            "controller" => "GreenCheap\\Info\\Controller\\InfoController",
        ],
    ],

    "menu" => [
        "system: info" => [
            "label" => "Info",
            "parent" => "system: system",
            "url" => "@system/info",
            "priority" => 30,
        ],
    ],
];
