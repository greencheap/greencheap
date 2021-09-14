<?php

return [
    "name" => "system/dashboard",

    "main" => "GreenCheap\\Dashboard\\DashboardModule",

    "autoload" => [
        "GreenCheap\\Dashboard\\" => "src",
    ],

    "routes" => [
        "/dashboard" => [
            "name" => "@dashboard",
            "controller" => "GreenCheap\\Dashboard\\Controller\\DashboardController",
        ],
    ],

    "resources" => [
        "system/dashboard:" => "",
    ],

    "menu" => [
        "dashboard" => [
            "layout" => "sidebar",
            "label" => "Dashboard",
            "icon" => "system/theme:assets/system-app-icons/dashboard.svg",
            "url" => "@dashboard",
            "active" => "@dashboard*",
            "priority" => 100,
        ],
    ],

    "config" => [
        "defaults" => [],
    ],
];
