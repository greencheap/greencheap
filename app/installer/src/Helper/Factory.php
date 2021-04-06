<?php

namespace GreenCheap\Installer\Helper;

use Composer\Config;
use Composer\Factory as BaseFactory;
use Composer\IO\IOInterface;

class Factory extends BaseFactory
{
    /**
     * @var array
     */
    protected static $config = [];

    /**
     * @param $config
     */
    public static function bootstrap($config)
    {
        self::$config = $config;
    }

    /**
     * @param IOInterface|null $io
     * @param null $cwd
     * @return Config
     */
    public static function createConfig(IOInterface $io = null, $cwd = null)
    {
        $config = new Config(true, $cwd);
        $config->merge(["config" => static::$config]);

        return $config;
    }
}
