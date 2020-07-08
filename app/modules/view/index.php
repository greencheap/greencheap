<?php

use GreenCheap\Event\PrefixEventDispatcher;
use GreenCheap\Twig\TwigEngine;
use GreenCheap\View\Asset\AssetFactory;
use GreenCheap\View\Asset\AssetManager;
use GreenCheap\View\Device;
use GreenCheap\View\Helper\DataHelper;
use GreenCheap\View\Helper\DeferredHelper;
use GreenCheap\View\Helper\AvatarHelper;
use GreenCheap\View\Helper\MapHelper;
use GreenCheap\View\Helper\MarkdownHelper;
use GreenCheap\View\Helper\MetaHelper;
use GreenCheap\View\Helper\ScriptHelper;
use GreenCheap\View\Helper\SectionHelper;
use GreenCheap\View\Helper\StyleHelper;
use GreenCheap\View\Helper\TokenHelper;
use GreenCheap\View\Helper\UrlHelper;
use GreenCheap\View\Loader\FilesystemLoader;
use GreenCheap\View\PhpEngine;
use GreenCheap\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\TemplateNameParser;

return [

    'name' => 'view',

    'include' => 'modules/*/index.php',

    'require' => [

        'view/twig'

    ],

    'main' => function ($app) {

        $app['view'] = function ($app) {
            return new View(new PrefixEventDispatcher('view.', $app['events']));
        };

        $app['assets'] = function () {
            return new AssetFactory();
        };

        $app['styles'] = function ($app) {
            return new AssetManager($app['assets']);
        };

        $app['scripts'] = function ($app) {
            return new AssetManager($app['assets']);
        };

        $app['module']->addLoader(function ($module) use ($app) {

            if (isset($module['views'])) {
                $app->extend('view', function ($view) use ($module) {
                    foreach ((array) $module['views'] as $name => $path) {
                        $view->map($name, $path);
                    }
                    return $view;
                });
            }

            return $module;
        });
    },

    'events' => [

        'controller' => [function ($event) use ($app) {

            $view = $app['view'];
            $layout = true;
            $result = $event->getControllerResult();

            if (is_array($result) && isset($result['$view'])) {

                foreach ($result as $key => $value) {
                    if ($key === '$view') {

                        if (isset($value['name'])) {
                            $name = $value['name'];
                            unset($value['name']);
                        }

                        if (isset($value['layout'])) {
                            $layout = $value['layout'];
                            unset($value['layout']);
                        }

                        $app->on('view.meta', function ($event, $meta) use ($value) {
                            $meta($value);
                        });

                    } elseif ($key[0] === '$') {

                        $view->data($key, $value);

                    }
                }

                if (isset($name)) {
                    $response = $result = $view->render($name, $result);
                }
            }

            if (!is_string($result)) {
                return;
            }

            if (is_string($layout)) {
                $view->map('layout', $layout);
            }

            if ($layout) {

                $view->section('content', (string) $result);

                if (null !== $result = $view->render('layout')) {
                    $response = $result;
                }
            }

            if (isset($response)) {
                $event->setResponse(new Response($response));
            }

        }, 50],

        'view.init' => [function ($event, $view) use ($app) {

            $view->addEngine(new PhpEngine(null, isset($app['locator']) ? new FilesystemLoader($app['locator']) : null));

            if (isset($app['twig'])) {
                $view->addEngine(new TwigEngine($app['twig'], new TemplateNameParser()));
            }

            $view->addGlobal('app', $app);
            $view->addGlobal('view', $view);

            $view->addHelpers([
                new DataHelper(),
                new DeferredHelper($app['events']),
                new AvatarHelper(),
                new MapHelper(),
                new MetaHelper(),
                new ScriptHelper($app['scripts']),
                new SectionHelper(),
                new StyleHelper($app['styles']),
                new UrlHelper($app['url'])
            ]);

            if (isset($app['csrf'])) {
                $view->addHelper(new TokenHelper($app['csrf']));
            }

            if (isset($app['markdown'])) {
                $view->addHelper(new MarkdownHelper($app['markdown']));
            }

        }, 50]

    ],

    'autoload' => [

        'GreenCheap\\View\\' => 'src'

    ]

];
