<?php

namespace GreenCheap\Cache\Controller;

use GreenCheap\Application as App;
use GreenCheap\Routing\Annotation\Request;
use GreenCheap\User\Annotation\Access;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class CacheController
 * @package GreenCheap\Cache\Controller
 * @Access(admin=true)
 */
class CacheController
{

    /**
     * @Request({"caches": "array"}, csrf=true)
     * @param $caches
     * @return array
     */
    #[ArrayShape(['message' => "string"])]
    public function clearAction($caches): array
    {
        App::module('system/cache')->clearCache($caches);

        return ['message' => 'success'];
    }
}
