<?php

namespace GreenCheap\System\Controller;

use GreenCheap\Application as App;
use GreenCheap\Config\Config;
use GreenCheap\Routing\Annotation\Request;
use GreenCheap\User\Annotation\Access;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @Access("system: access settings", admin=true)
 */
class SettingsController
{
    /**
     * @return array[]
     */
    #[ArrayShape(['$view' => "array"])]
    public function indexAction(): array
    {
        return [
            '$view' => [
                "title" => __("Settings"),
                "name" => "system:modules/settings/views/settings.php",
            ],
        ];
    }

    /**
     * @Request({"config": "array", "options": "array"}, csrf=true)
     * @param array $values
     * @param array $options
     * @return array
     */
    #[ArrayShape(['message' => "string"])]
    public function saveAction($values = [], $options = []): array
    {
        $config = new Config();
        $config->merge(include $file = App::get("config.file"));

        foreach ($values as $module => $value) {
            $config->set($module, $value);
        }

        file_put_contents($file, $config->dump());

        foreach ($options as $module => $value) {
            $this->configAction($module, $value);
        }

        if (function_exists("opcache_invalidate")) {
            opcache_invalidate($file);
        }

        return ["message" => "success"];
    }

    /**
     * @Request({"name", "config": "array"}, csrf=true)
     * @param $name
     * @param array $config
     * @return array
     */
    #[ArrayShape(['message' => "string"])]
    public function configAction($name, $config = []): array
    {
        App::config()->set($name, array_replace(App::config($name)->toArray(), $config));

        return ["message" => "success"];
    }
}
