<?php
namespace GreenCheap\Installer\Controller;
use GreenCheap\Application as App;

/**
 * @Access("system: manage packages", admin=true)
 */
class MarketplaceController
{
    /**
     * @Request({"filter":"array" , "page":"int"})
     */
    public function indexAction( array $filter = [] , int $page = null )
    {
        return [
            '$view' => [
                'title' => __('Marketplace'),
                'name'  => 'installer:views/marketplace.php'
            ],
            '$data' => [
                'config'   => [
                    'filter' => (object) $filter,
                    'page'   => $page
                ],
                'installedPackages' => array_values(array_merge(App::package()->all('greencheap-theme') , App::package()->all('greencheap-extension'))),
            ]
        ];
    }
}
