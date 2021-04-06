<?php

use GreenCheap\Content\ContentHelper;
use GreenCheap\Content\Plugin\MarkdownPlugin;
use GreenCheap\Content\Plugin\SimplePlugin;
use GreenCheap\Content\Plugin\VideoPlugin;

return [
    "name" => "system/content",

    "main" => function ($app) {
        $app->subscribe(new MarkdownPlugin(), new SimplePlugin(), new VideoPlugin());

        $app["content"] = function () {
            return new ContentHelper();
        };
    },

    "autoload" => [
        "GreenCheap\\Content\\" => "src",
    ],
];
