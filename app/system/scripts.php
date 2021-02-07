<?php
use Doctrine\DBAL\Schema\Comparator;

return [

    'install' => function ($app) {

        $db = $app['db'];
        $util = $db->getUtility();

        if ($util->tableExists('@system_auth') === false) {
            $util->createTable('@system_auth', function ($table) {
                $table->addColumn('id', 'string', ['length' => 255]);
                $table->addColumn('user_id', 'integer', ['unsigned' => true, 'length' => 10, 'default' => 0]);
                $table->addColumn('access', 'datetime', ['notnull' => false]);
                $table->addColumn('status', 'smallint');
                $table->addColumn('data', 'json_array', ['notnull' => false]);
                $table->setPrimaryKey(['id']);
            });
        }

        if ($util->tableExists('@system_config') === false) {
            $util->createTable('@system_config', function ($table) {
                $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
                $table->addColumn('name', 'string', ['length' => 255, 'default' => '']);
                $table->addColumn('value', 'text');
                $table->setPrimaryKey(['id']);
                $table->addUniqueIndex(['name'], '@SYSTEM_CONFIG_NAME');
            });
        }

        if ($util->tableExists('@system_node') === false) {
            $util->createTable('@system_node', function ($table) {
                $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
                $table->addColumn('parent_id', 'integer', ['unsigned' => true, 'length' => 10, 'default' => 0]);
                $table->addColumn('priority', 'integer', ['default' => 0]);
                $table->addColumn('status', 'smallint');
                $table->addColumn('title', 'string', ['length' => 255]);
                $table->addColumn('slug', 'string', ['length' => 255]);
                $table->addColumn('path', 'string', ['length' => 1023]);
                $table->addColumn('link', 'string', ['length' => 255]);
                $table->addColumn('type', 'string', ['length' => 255]);
                $table->addColumn('menu', 'string', ['length' => 255]);
                $table->addColumn('roles', 'simple_array', ['notnull' => false]);
                $table->addColumn('data', 'json_array', ['notnull' => false]);
                $table->setPrimaryKey(['id']);
            });
        }

        if ($util->tableExists('@system_page') === false) {
            $util->createTable('@system_page', function ($table) {
                $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
                $table->addColumn('title', 'string', ['length' => 255]);
                $table->addColumn('content', 'text');
                $table->addColumn('data', 'json_array', ['notnull' => false]);
                $table->setPrimaryKey(['id']);
            });
        }

        if ($util->tableExists('@system_role') === false) {
            $util->createTable('@system_role', function ($table) {
                $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
                $table->addColumn('name', 'string', ['length' => 255]);
                $table->addColumn('priority', 'integer', ['default' => 0]);
                $table->addColumn('permissions', 'simple_array', ['notnull' => false]);
                $table->setPrimaryKey(['id']);
                $table->addUniqueIndex(['name'], '@SYSTEM_ROLE_NAME');
                $table->addIndex(['name', 'priority'], '@SYSTEM_ROLE_NAME_PRIORITY');
            });

            /**
             * @todo DriverManager 2.12.1 insert hatası
             * @body prefix @system_role olduğu gibi görüyor, normalde gc_system_role çevirmesi gerekir.
             */
            $db->insert('@system_role', ['id' => 1, 'name' => 'Anonymous', 'priority' => 0]);
            $db->insert('@system_role', ['id' => 2, 'name' => 'Authenticated', 'priority' => 1, 'permissions' => 'blog: post comments']);
            $db->insert('@system_role', ['id' => 3, 'name' => 'Administrator', 'priority' => 2]);
        }

        if ($util->tableExists('@system_session') === false) {
            $util->createTable('@system_session', function ($table) {
                $table->addColumn('id', 'string', ['length' => 255]);
                $table->addColumn('time', 'datetime');
                $table->addColumn('data', 'text', ['length' => 65532]);
                $table->setPrimaryKey(['id']);
            });
        }

        if ($util->tableExists('@system_user') === false) {
            $util->createTable('@system_user', function ($table) {
                $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
                $table->addColumn('name', 'string', ['length' => 255, 'default' => '']);
                $table->addColumn('username', 'string', ['length' => 255, 'default' => '']);
                $table->addColumn('email', 'string', ['length' => 255, 'default' => '']);
                $table->addColumn('password', 'string', ['length' => 255, 'default' => '']);
                $table->addColumn('url', 'string', ['length' => 255, 'default' => '']);
                $table->addColumn('status', 'smallint', ['default' => 0]);
                $table->addColumn('registered', 'datetime');
                $table->addColumn('login', 'datetime', ['notnull' => false]);
                $table->addColumn('activation', 'string', ['length' => 255, 'notnull' => false]);
                $table->addColumn('roles', 'simple_array', ['notnull' => false]);
                $table->addColumn('data', 'json_array', ['notnull' => false]);
                $table->setPrimaryKey(['id']);
                $table->addUniqueIndex(['username'], '@SYSTEM_USER_USERNAME');
                $table->addUniqueIndex(['email'], '@SYSTEM_USER_EMAIL');
            });
        }

        if ($util->tableExists('@system_widget') === false) {
            $util->createTable('@system_widget', function ($table) {
                $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
                $table->addColumn('title', 'string', ['length' => 255]);
                $table->addColumn('type', 'string', ['length' => 255]);
                $table->addColumn('status', 'smallint');
                $table->addColumn('nodes', 'simple_array', ['notnull' => false]);
                $table->addColumn('roles', 'simple_array', ['notnull' => false]);
                $table->addColumn('data', 'json_array', ['notnull' => false]);
                $table->setPrimaryKey(['id']);
            });
        }

        if ($util->tableExists('@system_notifications') === false) {
            $util->createTable('@system_notifications', function ($table) {
                $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
                $table->addColumn('title', 'string', ['length' => 255]);
                $table->addColumn('image', 'string', ['length' => 255]);
                $table->addColumn('except_group' , 'simple_array' , ['notnull' => false]);
                $table->addColumn('except_user' , 'simple_array' , ['notnull' => false]);
                $table->addColumn('read_group' , 'simple_array' , ['notnull' => false]);
                $table->addColumn('read_user' , 'simple_array' , ['notnull' => false]);
                $table->addColumn('date' , 'datetime');
                $table->addColumn('data', 'json_array', ['notnull' => false]);
                $table->setPrimaryKey(['id']);
            });
        }

        if ($util->tableExists('@system_comments') === false) {
            $util->createTable('@system_comments', function ($table) {
                $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
                $table->addColumn('parent_id', 'integer', ['unsigned' => true, 'length' => 10]);
                $table->addColumn('own_id', 'integer', ['unsigned' => true, 'length' => 10]);
                $table->addColumn('type' , 'string');
                $table->addColumn('user_id', 'string', ['length' => 255]);
                $table->addColumn('created', 'datetime');
                $table->addColumn('content', 'text');
                $table->addColumn('status', 'smallint');
                $table->addColumn('data', 'json_array' , ['notnull' => false]);
                $table->setPrimaryKey(['id']);
            });
        }

        $app['config']->set('system/site', [
            'menus' => ['main' => ['id' => 'main', 'label' => 'Main']]
        ]);

        $app['config']->set('system/dashboard', [
            [
                [
                    'type' => 'feed',
                    'unix_id' => 1608983060446,
                    'count' => '3',
                    'url' => 'http://greencheap.net/blog/feed',
                    'content' => '1',
                    'title' => 'GCNEWS',
                ]
            ],
            [
                [
                    'type' => 'location',
                    'unix_id' => 1608983062675,
                    'units' => 'metric',
                    'uid' => 323786,
                    'city' => 'Ankara',
                    'country' => 'TR',
                    'coords' => [
                        'lat' => 39.9199,
                        'lon' => 32.8543,
                    ]
                ]
            ],
            [
                [
                    'type' => 'user',
                    'unix_id' => 1608983065298,
                    'show' => 'login',
                    'display' => 'list',
                    'total' => '',
                    'count' => 12,
                ]
            ]
        ]);

    },

    'updates' => [

        '0.11.3' => function ($app) {

            $db = $app['db'];
            $util = $db->getUtility();

            foreach (['@system_auth', '@system_config', '@system_node', '@system_page', '@system_role', '@system_session', '@system_user', '@system_widget'] as $name) {
                $table = $util->getTable($name);

                foreach ($table->getIndexes() as $name => $index) {
                    if ($name !== 'primary') {
                        $table->renameIndex($index->getName(), $app['db']->getPrefix() . $index->getName());
                    }
                }

                if ($app['db']->getDatabasePlatform()->getName() === 'sqlite') {
                    foreach ($table->getColumns() as $column) {
                        if (in_array($column->getType()->getName(), ['string', 'text'])) {
                            $column->setOptions(['customSchemaOptions' => ['collation' => 'NOCASE']]);
                        }
                    }
                }
            }

            $util->migrate();
        },

        '1.0.7' => function ($app) {

            $dashboard = $app->module('system/dashboard');
            $widgets = $dashboard->getWidgets();

            $ids = array_filter(array_keys($widgets), function ($id) use ($widgets){
                return $id == $widgets[$id]['id'];
            });

            $dashboard->saveWidgets(array_intersect_key($widgets, array_flip($ids)));
        },

        '2.0.7' => function ($app) {
            $util = $app['db']->getUtility();

            if($util->tableExists('@system_categories') === false) {
                $table =  $util->listTableDetails('@system_categories');
				if (!$table->hasColumn('type')) {
                    $app['db']->executeQuery('ALTER TABLE @system_categories ADD "type" varchar(255) NULL DEFAULT NULL');
				}
            }
        },

        '3.0.1' => function ($app) {
            $util = $app['db']->getUtility();
            $manager = $util->getSchemaManager();

            if($util->tableExists('@system_comments')) {
                $fromTable =  $util->listTableDetails('@system_comments');
				if (!$fromTable->hasColumn('data')) {
				    $toTable = clone $fromTable;
				    $toTable->addColumn('data', 'json_array' , ['notnull' => false]);
                    $comparator = new Comparator;
                    $manager->alterTable($comparator->diffTable($fromTable, $toTable));
				}
            }

            $app['config']->set('system/dashboard', [
                [],
                [],
                []
            ]);

            if($util->tableExists('@system_categories')) {
                $util->dropTable('@system_categories');
            }

        }
    ]

];
