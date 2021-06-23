<?php

namespace GreenCheap\Site\Controller;

use GreenCheap\Application as App;
use GreenCheap\Site\Model\Page;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class PageController
 * @package GreenCheap\Site\Controller
 */
class PageController
{
    /**
     * @param int $id
     * @return array
     */
    #[ArrayShape(['$view' => "array", 'page' => "\GreenCheap\Site\Model\Page", 'node' => "mixed"])]
    public static function indexAction(int $id = 0): array
    {
        if (!$page = Page::find($id)) {
            App::abort(404, __('Page not found.'));
        }

        $page->content = App::content()->applyPlugins($page->content, ['page' => $page, 'markdown' => $page->get('markdown')]);

        return [
            '$view' => [
                'title' => $page->title,
                'name'  => 'system/site/page.php'
            ],
            'page' => $page,
            'node' => App::node()
        ];
    }
}
