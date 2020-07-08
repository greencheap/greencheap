<?php

use GreenCheap\Filter\FilterManager;

return [

    'name' => 'filter',

    'main' => function ($app) {

        $app['filter'] = function() {
            return new FilterManager($this->config['defaults']);
        };

    },

    'autoload' => [

        'GreenCheap\\Filter\\' => 'src'

    ],

    'config' => [

        'defaults' => null

    ]
];
