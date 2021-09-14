<?php

return [

    'name' => 'system/text',

    'label' => 'Text',

    'icon' => 'system/theme:assets/widget-app-icons/text.svg',

    'render' => function ($widget) use ($app) {
        return $app['view']->render('system/site/widget-text.php', compact('widget'));
    },

    'events' => [

        'view.scripts' => function ($event, $scripts) {
            $scripts->register('widget-text', 'system/site:app/bundle/widget-text.js', '~widgets');
        }

    ]

];
