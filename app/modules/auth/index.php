<?php

use GreenCheap\Auth\Auth;
use GreenCheap\Auth\Encoder\NativePasswordEncoder;
use GreenCheap\Auth\Handler\DatabaseHandler;
use RandomLib\Factory;

return [
    "name" => "auth",

    "main" => function ($app) {
        $app["auth"] = function ($app) {
            return new Auth($app["events"], $app["auth.handler"]);
        };

        $app["auth.password"] = function () {
            return new NativePasswordEncoder();
        };

        $app["auth.random"] = function () {
            return (new Factory())->getLowStrengthGenerator();
        };

        $app["auth.handler"] = function ($app) {
            return new DatabaseHandler($app["db"], $app["request.stack"], $app["cookie"], $app["auth.random"], $this->config);
        };
    },

    "autoload" => [
        "GreenCheap\\Auth\\" => "src",
    ],

    "config" => [
        "timeout" => 900,
        "table" => "auth",
        "cookie" => [
            "name" => "",
            "lifetime" => 315360000,
        ],
    ],
];
