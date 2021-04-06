<?php

namespace GreenCheap\Site\Event;

use GreenCheap\Application as App;
use GreenCheap\Event\EventSubscriberInterface;
use GreenCheap\Site\Model\Page;

class PageListener implements EventSubscriberInterface
{
    public function onNodeSave($event, $request)
    {
        if (null === ($node = ($request->get("node") or null === ($data = $request->get("page")))) or "page" !== @$node["type"]) {
            return;
        }

        $page = $this->getPage(@$node["id"]);
        $page->save($data);

        $node["data"]["defaults"] = ["id" => $page->id];
        $node["link"] = "@page/" . $page->id;

        $request->request->set("node", $node);
    }

    public function onNodeDeleted($event, $node)
    {
        if ("page" !== $node->type) {
            return;
        }

        $page = $this->getPage($node->get("defaults.id", 0));

        if ($page->id) {
            $page->delete();
        }
    }

    public function onRouteConfigure($event, $route, $routes)
    {
        if ($route->getName() === "@page") {
            $routes->remove("@page");
            $route->setName("@page/" . $route->getDefault("id"));
            $routes->add($route->getName(), $route);
            // Custom Symfony 4
            $route->setOption("utf8", true);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            "before@site/api/node/save" => "onNodeSave",
            "before@site/api/node/save_1" => "onNodeSave",
            "model.node.deleted" => "onNodeDeleted",
            "route.configure" => "onRouteConfigure",
        ];
    }

    /**
     * Find page entity by node.
     *
     * @param  int $id
     * @return Page
     */
    protected function getPage($id)
    {
        if (!$id or !($page = Page::find($id))) {
            $page = Page::create();
        }

        return $page;
    }
}
