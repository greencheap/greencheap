<?php

return [

    'name' => 'system/theme',

    'type' => 'theme',

    'layout' => 'views:system/template.php',

    'events' => [

       'view.data' => function ($event, $data) use ($app) {
            if (!$app->isAdmin()) {
                return;
            }
        },

        'view.meta' => [function($event, $meta) use ($app) {
            $meta([
                'link:favicon' => [
                    'href' => $app['url']->getStatic('system/theme:favicon.ico'),
                    'rel' => 'shortcut icon',
                    'type' => 'image/x-icon'
                ],
                'link:appicon' => [
                    'href' => $app['url']->getStatic('system/theme:apple_touch_icon.png'),
                    'rel' => 'apple-touch-icon-precomposed'
                ]
            ]);
        }, 10],

        'view.layout' => function ($event, $view) use ($app) {

            if (!$app->isAdmin()) {
                return;
            }

            $user = $app['user'];

            $view->data('$greencheap', [
                'editor' => $app->module('system/editor')->config(),
                'storage' => $app->module('system/finder')->config('storage'),
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'username' => $user->username,
                    'avatar' => $user->getAvatar()
                ],
                'darkMode' => $user->get('darkMode') ?? false,
                'menu' => array_values($app['system']->getMenu()->getItems()),

            ]);

            $view->data('$client' , [
                'version' => $app->version(),
                'system_api' => $app->get('system.api'),
                'settings' => $app['system']->config(['beta']),
                'project_uri' => $app->url()->base(0),
                'project_title' => $app->config('system/site')->get('title'),
                'client_php_version' => PHP_VERSION
            ]);

            $subsets = 'latin,latin-ext';
            $subset  = __('_subset');

            if ('cyrillic' == $subset) {
    			$subsets .= ',cyrillic,cyrillic-ext';
    		} elseif ('greek' == $subset) {
    			$subsets .= ',greek,greek-ext';
    		} elseif ('vietnamese' == $subset) {
    			$subsets .= ',vietnamese';
    		}

            $event['subset'] = $subsets;
            $event['pageClass'] = str_replace('/','-', str_replace('/admin/', '', parse_url( $app['url']->current(true),PHP_URL_PATH )));

        }

    ],

    'resources' => [

        'system/theme:' => '',
        'views:system' => 'views'

    ]

];
