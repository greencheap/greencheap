<?php

use GreenCheap\Comment\CommentPlugin;

return [

    'name' => 'system/comment',

    'main' => function ($app) {
        $app->subscribe(new CommentPlugin);
    },

    'autoload' => [
        'GreenCheap\\Comment\\' => 'src'
    ]

];
