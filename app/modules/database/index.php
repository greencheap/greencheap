<?php

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use GreenCheap\Database\ORM\EntityManager;
use GreenCheap\Database\ORM\Loader\AnnotationLoader;
use GreenCheap\Database\ORM\MetadataManager;
use GreenCheap\Event\PrefixEventDispatcher;

$config = [

    'name' => 'database',

    'main' => function ($app) {

        $default = [
            'wrapperClass' => 'GreenCheap\Database\Connection'
        ];

        $app['dbs'] = function () use ($default) {

            $dbs = [];
            foreach ($this->config['connections'] as $name => $params) {
                $dbs[$name] = DriverManager::getConnection(array_replace($default, $params));
            }
            return $dbs;
        };

        $app['db'] = function ($app) {
            return $app['dbs'][$this->config['default']];
        };

        $app['db.em'] = function ($app) {
            return new EntityManager($app['db'], $app['db.metas'], $app['db.events']);
        };

        $app['db.metas'] = function ($app) {

            $manager = new MetadataManager($app['db'], $app['db.events']);
            $manager->setLoader(new AnnotationLoader);
            $manager->setCache($app['cache.phpfile']);

            return $manager;
        };

        $app['db.events'] = function ($app) {
            return new PrefixEventDispatcher('model.', $app['events']);
        };

        $app['db.debug_stack'] = function () {
            return new DebugStack();
        };

        Type::overrideType(Types::SIMPLE_ARRAY, '\GreenCheap\Database\Types\SimpleArrayType');
        Type::overrideType(Types::JSON_ARRAY, '\GreenCheap\Database\Types\JsonArrayType');
    },

    'autoload' => [

        'GreenCheap\\Database\\' => 'src'

    ],

    'config' => [

        'default' => 'sqlite',

        'connections' => [

            'mysql' => [

                'driver'   => 'pdo_mysql',
                'dbname'   => '',
                'host'     => 'localhost',
                'user'     => 'root',
                'password' => '',
                'engine'   => 'InnoDB',
                'charset'  => 'utf8',
                'collate'  => 'utf8_unicode_ci',
                'prefix'   => ''

            ],

            'pgsql' => [

                'driver'   => 'pdo_pgsql',
                'dbname'   => '',
                'host'     => 'localhost',
                'user'     => 'root',
                'password' => '',
                'charset'  => 'utf8',
                'prefix'   => '',
                'port'     => 5432

            ],

            'sqlite' => [

                'driver' => 'pdo_sqlite',
                'path' => "greencheap.db",
                'charset' => 'utf8',
                'prefix' => 'gc_',
                'driverOptions' => [
                    'userDefinedFunctions' => [
                        'REGEXP' => [
                            'callback' => function ($pattern, $subject) {
                                return preg_match("/$pattern/", $subject);
                            },
                            'numArgs' => 2
                        ]
                    ]
                ]

            ]

        ]

    ]

];

if (defined('PDO::MYSQL_ATTR_INIT_COMMAND')) {
    $config['config']['connections']['mysql']['driverOptions'] = [
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8 COLLATE utf8_unicode_ci'
    ];
}

return $config;
