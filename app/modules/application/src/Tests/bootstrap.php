<?php

$autoload = [
    'GreenCheap\\Auth\\' => '/app/modules/auth/src',
    'GreenCheap\\Config\\' => '/app/modules/config/src',
    'GreenCheap\\Cookie\\' => '/app/modules/cookie/src',
    'GreenCheap\\Database\\' => '/app/modules/database/src',
    'GreenCheap\\Filesystem\\' => '/app/modules/filesystem/src',
    'GreenCheap\\Filter\\' => '/app/modules/filter/src',
    'GreenCheap\\Migration\\' => '/app/modules/migration/src',
    'GreenCheap\\Package\\' => '/app/modules/package/src',
    'GreenCheap\\Routing\\' => '/app/modules/routing/src',
    'GreenCheap\\Session\\' => '/app/modules/session/src',
    'GreenCheap\\Tree\\' => '/app/modules/tree/src',
    'GreenCheap\\View\\' => '/app/modules/view/src'
];

$path = realpath(__DIR__.'/../../../../../');
$loader = require $path.'/autoload.php';

foreach ($autoload as $namespace => $src) {
    $loader->addPsr4($namespace, $path.$src);
}
