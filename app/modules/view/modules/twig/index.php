<?php

use GreenCheap\Twig\TwigCache;
use GreenCheap\Twig\TwigLoader;
use GreenCheap\View\Loader\FilesystemLoader;
use Symfony\Component\Templating\Loader\FilesystemLoader as SymfonyFilesystemLoader;
use Twig\Extension\DebugExtension;
use Twig\Environment;

return [

    'name' => 'view/twig',

    'main' => function ($app) {

        $app['twig'] = function ($app) {
            $twig = new Environment(new TwigLoader(isset($app['locator']) ? new FilesystemLoader($app['locator']) : new SymfonyFilesystemLoader([])), [
                'cache' => new TwigCache($app['path.cache']),
                'auto_reload' => true,
                'debug' => $app['debug'],
            ]);

            if (isset($app['debug']) && $app['debug']) {
                $twig->addExtension(new DebugExtension());
            }
            return $twig;
         };

    },

    'autoload' => [

        'GreenCheap\\Twig\\' => 'src'

    ]

];
