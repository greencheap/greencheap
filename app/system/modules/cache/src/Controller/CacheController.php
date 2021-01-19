<?php

namespace GreenCheap\Cache\Controller;

use GreenCheap\Application as App;
use GreenCheap\Routing\Annotation\Request;
use GreenCheap\User\Annotation\Access;

/**
 * @Access(admin=true)
 */
class CacheController
{
    /**
     * @Request({"caches": "array"}, csrf=true)
     */
    public function clearAction($caches)
    {
        App::module('system/cache')->clearCache($caches);

        return ['message' => 'success'];
    }
}
