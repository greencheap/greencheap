<?php

namespace GreenCheap\Module\Loader;

interface LoaderInterface
{
    /**
     * Loads the module.
     *
     * @param  mixed $module
     * @return mixed
     */
    public function load($module);
}
