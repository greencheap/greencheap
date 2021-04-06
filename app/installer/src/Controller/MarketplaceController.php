<?php
namespace GreenCheap\Installer\Controller;
use GreenCheap\Application as App;
use GreenCheap\Routing\Annotation\Request;
use GreenCheap\User\Annotation\Access;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @Access("system: manage packages", admin=true)
 */
class MarketplaceController
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
                "title" => __("Marketplace"),
                "name" => "installer:views/marketplace.php",
            ],
            '$data' => [
                "config" => [
                    "filter" => (object) $filter,
                    "page" => $page,
                ],
                "installedPackages" => array_values(array_merge(App::package()->all("greencheap-theme"), App::package()->all("greencheap-extension"))),
            ],
        ];
    }
}
