<?php

namespace GreenCheap\Module;

use GreenCheap\Application as App;

interface ModuleInterface
{
    /**
     * Main bootstrap method.
     *
     * @param App $app
     */
    public function main(App $app);

    /**
     * Gets a option value.
     *
     * @param  mixed $key
     * @param  mixed $default
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * Gets a config value.
     *
     * @param  mixed $key
     * @param  mixed $default
     * @return mixed
     */
    public function config($key = null, $default = null);

    /**
     * @return mixed
     */
    public function getCompatibleSystemVersion();

    /**
     * @param null $key
     * @return mixed
     */
    public function getMarketplace($key = null);

    /**
     * @param $version
     * @param $version_seconds
     * @return boolean
     */
    public function isCompatibleSystem($version, $version_seconds);

    /**
     * @return mixed
     */
    public function getRequirements();

    /**
     * @return mixed
     */
    public function runMarketplaceFile();
}
