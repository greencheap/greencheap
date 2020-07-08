<?php

use GreenCheap\Application as App;
use GreenCheap\Application\Console\Application as Console;
use GreenCheap\Module\Loader\AutoLoader;
use GreenCheap\Module\Loader\ConfigLoader;

$loader = require $path.'/autoload.php';

$app = new App($config);
$app['autoloader'] = $loader;

$app['module']->register([
    'packages/*/*/index.php',
    'app/modules/*/index.php',
    'app/installer/index.php',
    'app/system/index.php',
    'app/console/index.php'
], $path);

$app['module']->addLoader(new AutoLoader($app['autoloader']));
$app['module']->addLoader(new ConfigLoader(require $path.'/app/system/config.php'));

if ($app['config.file']) {
    $app['module']->addLoader(new ConfigLoader(require $app['config.file']));
    $app['module']->load('system');
}
$app['module']->load('console');

$console = new Console($app, 'GreenCheap', $app->version());
$console->run();
