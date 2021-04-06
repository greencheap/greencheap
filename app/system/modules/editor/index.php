<?php

return [
    "name" => "system/editor",

    "autoload" => [
        "GreenCheap\\Editor\\" => "src",
    ],

    "config" => [
        "editor" => "html",
        "mode" => "",
    ],

    "resources" => [
        "system/editor:" => "",
    ],

    "events" => [
        "view.scripts" => function ($event, $scripts) use ($app) {
            $scripts->register("editor", "system/editor:app/bundle/editor.js", ["input-link"]);
            $scripts->register(
                "editor-data",
                sprintf(
                    'var $editor = %s;',
                    json_encode([
                        "root_url" => $app["url"]->getStatic(__DIR__),
                        "locale" => $app->module("system/intl")->getLocale(),
                        "content_css" => [$app["url"]->getStatic("theme:css/theme.css")],
                    ])
                ),
                ["~editor"],
                "string"
            );
        },
    ],
];
