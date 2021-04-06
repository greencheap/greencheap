<?php

use GreenCheap\Feed\FeedFactory;

return [
    "name" => "feed",

    "main" => function ($app) {
        $app["feed"] = function () {
            return new FeedFactory();
        };
    },

    "autoload" => [
        "GreenCheap\\Feed\\" => "src",
    ],
];
