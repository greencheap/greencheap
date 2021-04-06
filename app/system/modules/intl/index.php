<?php

return [
    "name" => "system/intl",

    "main" => "GreenCheap\\Intl\\IntlModule",

    "autoload" => [
        "GreenCheap\\Intl\\" => "src",
    ],

    "resources" => [
        "system/intl:" => "",
    ],

    "routes" => [
        "/system/intl" => [
            "name" => "@system/intl",
            "controller" => "GreenCheap\\Intl\\Controller\\IntlController",
        ],
    ],

    "config" => [
        "locale" => "en_GB",
    ],

    "events" => [
        "view.init" => function ($event, $view) {
            $view->addGlobal("intl", $this);
        },
    ],
];
