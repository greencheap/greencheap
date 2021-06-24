<?php

namespace GreenCheap\Module;

use GreenCheap\Application as App;
use GreenCheap\Event\EventSubscriberInterface;
use GreenCheap\Util\Arr;

class Module implements ModuleInterface, EventSubscriberInterface
{
    /**
     * @var string
     */
    public string $name;

    /**
     * @var string
     */
    public string $path;

    /**
     * @var array
     */
    public array $config;

    /**
     * @var array
     */
    public array $options;

    /**
     * @var array
     */
    public array $marketplace = [];

    /**
     * Constructor.
     *
     * @param array $options
     * @param App $app
     */
    public function __construct(array $options = [])
    {
        $this->name = $options["name"];
        $this->path = $options["path"];
        $this->config = $options["config"];
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function main(App $app)
    {
        $main = $this->options["main"];

        if ($main instanceof \Closure) {
            $main = $main->bindTo($this, $this);
        }

        if (is_callable($main)) {
            return call_user_func($main, $app);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        if (is_array($key)) {
            return Arr::extract($this->options, $key);
        }

        return Arr::get($this->options, $key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function config($key = null, $default = null)
    {
        if (is_array($key)) {
            return Arr::extract($this->config, $key);
        }

        return Arr::get($this->config, $key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return isset($this->options["events"]) ? $this->options["events"] : [];
    }

    /**
     * {@inheritdoc}
     */
    public function runMarketplaceFile()
    {
        if (App::file()->exists($this->path . "/marketplace.json")) {
            $this->marketplace = (array) json_decode(file_get_contents($this->path . "/marketplace.json"));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCompatibleSystemVersion()
    {
        return $this->getMarketplace("version");
    }

    /**
     * {@inheritdoc}
     */
    public function getMarketplace($key = null, $default = null)
    {
        if (is_array($key)) {
            return Arr::extract($this->marketplace, $key);
        }

        return Arr::get($this->marketplace, $key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function isCompatibleSystem($version, $version_seconds): bool
    {
        $version_seconds = $this->versionCompare($version_seconds);
        /**
        if(version_compare($version , $version_seconds['version'] , $version_seconds['compare']) || $version_seconds['version'] === "*"){
            return true;
        }**/
        //return false;

        return true;
    }

    /**
     * @inheritDoc
     */
    public function getRequirements()
    {
        return (array) $this->getMarketplace("requirements");
    }

    /**
     * @param $version
     * @return array
     */
    protected function versionCompare($version): array
    {
        if ($version === "*" || !$version) {
            return [
                "compare" => "*",
                "version" => "*",
            ];
        }
        preg_match("/([<|=|>]+)?([0-9\.]+)/", $version, $matches);

        if (count($matches) != 3) {
            return [
                "compare" => ">=",
                "version" => $matches[1],
            ];
        }

        return [
            "compare" => $matches[1],
            "version" => $matches[2],
        ];
    }
}
