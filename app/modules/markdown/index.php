<?php

use GreenCheap\Markdown\Markdown;

return [

    'name' => 'markdown',

    'main' => function ($app) {

        $app['markdown'] = function() {
            return new Markdown;
        };

    },

    'autoload' => [

        'GreenCheap\\Markdown\\' => 'src'

    ]

];
