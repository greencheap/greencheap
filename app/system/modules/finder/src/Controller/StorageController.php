<?php

namespace GreenCheap\Finder\Controller;

use GreenCheap\Application as App;
use GreenCheap\User\Annotation\Access;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @Access("system: manage storage", admin=true)
 */
class StorageController
{
    /**
     * @return array
     */
    #[ArrayShape(['$view' => "array", 'root' => "mixed", 'mode' => "string"])]
    public function indexAction():array
    {
        return [
            '$view' => [
                'title' => __('Storage'),
                'name'  => 'system:modules/finder/views/storage.php'
            ],
            'root' => App::module('system/finder')->config('storage'),
            'mode' => 'write'
        ];
    }
}
