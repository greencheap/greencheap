<?php

namespace GreenCheap\Site\Controller;

use GreenCheap\Application as App;
use GreenCheap\Routing\Annotation\Request;
use GreenCheap\Routing\Annotation\Route;
use GreenCheap\Site\Model\Node;
use GreenCheap\User\Annotation\Access;
use GreenCheap\User\Model\Role;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class NodeController
 * @package GreenCheap\Site\Controller
 */
class NodeController
{
    /**
     * @var
     */
    protected $site;

    /**
     * NodeController constructor.
     */
    public function __construct()
    {
        $this->site = App::module('system/site');
    }

    /**
     * @Route("site/page", name="page")
     * @Access("site: manage site", admin=true)
     */
    public function indexAction(): array
    {
        if ($test = Node::fixOrphanedNodes()) {
            return App::redirect('@site/page');
        }

        return [
            '$view' => [
                'title' => __('Pages'),
                'name'  => 'system/site/admin/index.php'
            ],
            '$data' => [
                'config' => [
                    'menus' => App::menu()->getPositions()
                ],
                'types' => array_values($this->site->getTypes())
            ]
        ];
    }

    /**
     * @Route("site/page/edit", name="page/edit")
     * @Access("site: manage site", admin=true)
     * @Request({"id", "menu"})
     * @param string $id
     * @param string $menu
     * @return array
     */
    #[ArrayShape(['$view' => "array", '$data' => "array"])]
    public function editAction($id = '', $menu = ''): array
    {
        if (is_numeric($id)) {

            if (!$id or !$node = Node::find($id)) {
                App::abort(404, 'Node not found.');
            }

        } else {
            $node = Node::create(['type' => $id]);

            if ($menu && !App::menu($menu)) {
                App::abort(404, 'Menu not found.');
            }

            $node->menu = $menu;
        }

        if (!$type = $this->site->getType($node->type)) {
            App::abort(404, 'Type not found.');
        }

        return [
            '$view' => [
                'title' => __('Pages'),
                'name'  => 'system/site/admin/edit.php'
            ],
            '$data' => [
                'node' => $node,
                'type' => $type,
                'roles' => array_values(Role::findAll())
            ]
        ];
    }

    /**
     * @Route("site/settings")
     * @Access("system: access settings", admin=true)
     */
    #[ArrayShape(['$view' => "array", '$data' => "array"])]
    public function settingsAction(): array
    {
        return [
            '$view' => [
                'title' => __('Settings'),
                'name'  => 'system/site/admin/settings.php'
            ],
            '$data' => [
                'config' => $this->site->config(['title', 'description', 'maintenance.', 'meta.', 'logo', 'icons.', 'code.', 'view.'])
            ]
        ];
    }

    /**
     * @Route("api/site/link", name="api/link")
     * @Request({"link"})
     * @Access("site: manage site")
     * @param $link
     * @return array
     */
    #[ArrayShape(['message' => "string", 'url' => "mixed"])]
    public function linkAction($link): array
    {
        return ['message' => 'success', 'url' => App::url($link, [], 'base') ?: $link];
    }
}
