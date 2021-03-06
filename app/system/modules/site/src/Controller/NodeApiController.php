<?php

namespace GreenCheap\Site\Controller;

use GreenCheap\Application as App;
use GreenCheap\Routing\Annotation\Request;
use GreenCheap\Routing\Annotation\Route;
use GreenCheap\Site\Model\Node;
use GreenCheap\User\Annotation\Access;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @Access("site: manage site")
 */
class NodeApiController
{
    /**
     * @Route("/", methods="GET")
     * @Request({"menu"})
     * @param bool $menu
     * @return array
     */
    public function indexAction($menu = false): array
    {
        $query = Node::query();

        if (is_string($menu)) {
            $query->where(['menu' => $menu]);
        }

        return array_values($query->get());
    }

    /**
     * @Route("/{id}", methods="GET", requirements={"id"="\d+"})
     * @param $id
     * @return Node|null
     */
    public function getAction($id)
    {
        if (!$node = Node::find($id)) {
            return App::jsonabort(404, __('Node not found.'));
        }

        return $node;
    }

    /**
     * @Route("/", methods="POST")
     * @Route("/{id}", methods="POST", requirements={"id"="\d+"})
     * @Request({"node": "array", "id": "int"}, csrf=true)
     * @param $data
     * @param int $id
     * @return array
     */
    #[ArrayShape(['message' => "string", 'node' => "\GreenCheap\Site\Model\Node"])]
    public function saveAction($data, $id = 0): array
    {
        if (!$node = Node::find($id)) {
            $node = Node::create();
            unset($data['id']);
        }

        if (!$data['slug'] = App::filter($data['slug'] ?: $data['title'], 'slugify')) {
            return App::jsonabort(400, __('Invalid slug.'));
        }

        $node->save($data);

        return ['message' => 'success', 'node' => $node];
    }

    /**
     * @Route("/{id}", methods="DELETE", requirements={"id"="\d+"})
     * @Request({"id": "int"}, csrf=true)
     * @param $id
     * @return array
     */
    #[ArrayShape(['message' => "string"])]
    public function deleteAction($id): array
    {
        if ($node = Node::find($id)) {

            if ($type = App::module('system/site')->getType($node->type) and isset($type['protected']) and $type['protected']) {
                return App::jsonabort(400, __('Invalid type.'));
            }

            $node->delete();
        }

        return ['message' => 'success'];
    }

    /**
     * @Route("/bulk", methods="POST")
     * @Request({"nodes": "array"}, csrf=true)
     * @param array $nodes
     * @return array
     */
    #[ArrayShape(['message' => "string"])]
    public function bulkSaveAction($nodes = []): array
    {
        foreach ($nodes as $data) {
            $this->saveAction($data, isset($data['id']) ? $data['id'] : 0);
        }

        return ['message' => 'success'];
    }

    /**
     * @Route("/bulk", methods="DELETE")
     * @Request({"ids": "array"}, csrf=true)
     * @param array $ids
     * @return array
     */
    #[ArrayShape(['message' => "string"])]
    public function bulkDeleteAction($ids = []): array
    {
        foreach (array_filter($ids) as $id) {
            $this->deleteAction($id);
        }

        return ['message' => 'success'];
    }

    /**
     * @Route("/updateOrder", methods="POST")
     * @Request({"menu", "nodes": "array"}, csrf=true)
     * @param $menu
     * @param array $nodes
     * @return array
     */
    #[ArrayShape(['message' => "string"])]
    public function updateOrderAction($menu, $nodes = []): array
    {
        foreach ($nodes as $data) {

            if ($node = Node::find($data['id'])) {

                $node->priority  = $data['order'];
                $node->menu      = $menu;
                $node->parent_id = $data['parent_id'] ?: 0;

                $node->save();
            }
        }

        return ['message' => 'success'];
    }

    /**
     * @Route("/frontpage", methods="POST")
     * @Request({"id": "int"}, csrf=true)
     * @param $id
     * @return array
     */
    #[ArrayShape(['message' => "string"])]
    public function frontpageAction($id): mixed
    {
        if (!$node = Node::find($id) or !$type = App::module('system/site')->getType($node->type)) {
            return App::jsonabort(404, __('Node not found.'));
        }

        if (isset($type['frontpage']) and !$type['frontpage']) {
            return App::jsonabort(400, __('Invalid node type.'));
        }

        App::config('system/site')->set('frontpage', $id);
        return ['message' => 'success'];
    }
}
