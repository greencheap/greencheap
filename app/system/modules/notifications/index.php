<?php
return [
    "name" => "system/notifications",

    "main" => function () {},

    "autoload" => [
        "GreenCheap\\Notifications\\" => "src",
    ],

    "routes" => [
        "notifications" => [
            "name" => "@notifications",
            "controller" => "GreenCheap\\Notifications\\Controller\\ApiNotifications",
        ],
    ],
];
?>
