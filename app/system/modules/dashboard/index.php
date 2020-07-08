<?php

return [

    'name' => 'system/dashboard',

    'main' => 'GreenCheap\\Dashboard\\DashboardModule',

    'autoload' => [

        'GreenCheap\\Dashboard\\' => 'src'

    ],

    'routes' => [

        '/dashboard' => [
            'name' => '@dashboard',
            'controller' => 'GreenCheap\\Dashboard\\Controller\\DashboardController'
        ]

    ],

    'resources' => [
        'system/dashboard:' => ''
    ],

    'menu' => [

        'dashboard' => [
            'layout' => 'sidebar',
            'label' => 'Dashboard',
            'icon' => 'system/dashboard:icon.svg',
            'url' => '@dashboard',
            'active' => '@dashboard*',
            'priority' => 100
        ]

    ],

    'config' => [

        'defaults' => []

    ]

];
