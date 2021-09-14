<?php
namespace GreenCheap\Installer\Controller;
use GreenCheap\Application as App;
use GreenCheap\Routing\Annotation\Request;
use GreenCheap\User\Annotation\Access;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @Access("system: manage packages", admin=true)
 */
class AppStoreController
{
    /**
     * @Request({"filter":"array" , "page":"int"})
     * @param array $filter
     * @param int|null $page
     * @return array
     */
    #[ArrayShape(['$view' => "array", '$data' => "array"])]
    public function indexAction(array $filter = [], int $page = null): array
    {
        return [
            '$view' => [
                "title" => __("Extension List"),
                "name" => "installer:views/marketplace.php",
            ],
            '$data' => [
                "config" => [
                    "filter" => (object) $filter,
                    "page" => $page,
                ],
                "installedPackages" => App::package()->all("greencheap-extension"),
            ],
        ];
    }

    /**
     * @Request({"filter":"array" , "page":"int"})
     * @param array $filter
     * @param int|null $page
     * @return array
     */
    #[ArrayShape(['$view' => "array", '$data' => "array"])]
    public function themesAction(array $filter = [], int $page = null): array
    {
        return [
            '$view' => [
                "title" => __("Themes List"),
                "name" => "installer:views/marketplace.php",
            ],
            '$data' => [
                "config" => [
                    "filter" => (object) $filter,
                    "page" => $page,
                ],
                "installedPackages" => App::package()->all("greencheap-theme"),
            ],
        ];
    }
}
