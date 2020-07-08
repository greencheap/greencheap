<?php

return [

    'name' => 'console',

    'autoload' => [

        'GreenCheap\\Console\\' => 'src'

    ],

    'events' => [

        'console.init' => function ($event, $console) {

            $namespace = 'GreenCheap\\Console\\Commands\\';

            foreach (glob(__DIR__ . '/src/Commands/*Command.php') as $file) {
                $class = $namespace . basename($file, '.php');
                $console->add(new $class);
            }

        }

    ],

    'require' => 'application'

];
