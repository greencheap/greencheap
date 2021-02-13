<?php

namespace GreenCheap\Installer\Controller;

use GreenCheap\Application as App;
use GreenCheap\Installer\Installer;
use GreenCheap\Routing\Annotation\Request;
use JetBrains\PhpStorm\ArrayShape;

class InstallerController
{
    /**
     * @var object
     */
    protected $installer;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $app = App::getInstance();
        $this->installer = new Installer($app);
    }

    /**
     * @return array
     */
    #[ArrayShape(['$view' => "array", '$installer' => "array", 'image' => "array"])]
    public function indexAction(): array
    {
        $intl = App::module('system/intl');
        return [
            '$view' => [
                'title' => __('GreenCheap Installer'),
                'name' => 'app/installer/views/installer.php',
            ],
            '$installer' => [
                'locale' => $intl->getLocale(),
                'locales' => $intl->getAvailableLanguages(),
                'sqlite' => class_exists('SQLite3') || (class_exists('PDO') && in_array('sqlite', \PDO::getAvailableDrivers(), true)),
                'pgsql' => in_array('pgsql', \PDO::getAvailableDrivers(), true)
            ],
            'image' => \GreenCheap\System\Controller\AdminController::getUnsplashImages()[array_rand(\GreenCheap\System\Controller\AdminController::getUnsplashImages() , 1)]
        ];
    }

    /**
     * @Request({"config": "array"})
     * @param array $config
     * @return array
     */
    public function checkAction($config = []): array
    {
        return $this->installer->check($config);
    }

    /**
     * @Request({"config": "array", "option": "array", "user": "array"})
     * @param array $config
     * @param array $option
     * @param array $user
     * @return array
     */
    public function installAction($config = [], $option = [], $user = []): array
    {
        return $this->installer->install($config, $option, $user);
    }

}
