<?php
return [
    'name' => 'system/categories',

    'autoload' => [
        'GreenCheap\\Categories\\' => 'src'
    ],

    'menu' => [
        'categories' => [
            'icon' => 'system/categories:icon.svg',
            'label' => 'Categories',
            'layout' => 'sidebar',
            'url' => '@categories/admin',
            'active' => '@categories/admin*',
            'priority' => 110
        ]
    ],

    'routes' => [
        'categories' => [
            'name' => '@categories',
            'controller' => 'GreenCheap\\Categories\\Controller\\CategoriesController'
        ],
        'api/categories' => [
            'name' => '@api/categories',
            'controller' => 'GreenCheap\\Categories\\Controller\\ApiCategoriesController'
        ]
    ],

    'config' => [
        'categories' => [
            'categories_per_page' => 25
        ]
    ],

    'permissions' => [
        'categories: manage categories' => [
            'title' => 'Manage categories',
            'description' => 'Approve, edit and delete categories'
        ]
    ],

    'resources' => [
        'system/categories:' => '',
        'views:system/categories' => 'views'
    ],
];
?>
