<?php

namespace GreenCheap\Routing\Loader;

use GreenCheap\Routing\Route;

interface LoaderInterface
{
    /**
     * Loads routes.
     *
     * @param  mixed $routes
     * @return Route[]
     */
    public function load($routes);
}
