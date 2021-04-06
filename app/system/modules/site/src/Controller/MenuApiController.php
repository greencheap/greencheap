<?php

namespace GreenCheap\Site\Controller;

use GreenCheap\Application as App;
use GreenCheap\Config\Config;
use GreenCheap\Kernel\Exception\ConflictException;
use GreenCheap\Routing\Annotation\Request;
use GreenCheap\Routing\Annotation\Route;
use GreenCheap\Site\Model\Node;
use GreenCheap\User\Annotation\Access;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @Access("site: manage site")
 */
class MenuApiController
{
    /**
     * @var Config
     */
    protected Config $config;

    /**
     * MenuApiController constructor.
     */
    public function __construct()
    {
        $this->config = App::config("system/site");
    }

    /**
     * @Route("/", methods="GET")
     */
    public function indexAction(): array
    {
        $menus = App::menu()->all();

        $menus["trash"] = ["id" => "trash", "label" => __("Trash"), "fixed" => true];

        foreach ($menus as &$menu) {
            $menu["count"] = Node::where(["menu" => $menu["id"]])->count();
        }

        if (!$menus["trash"]["count"]) {
            unset($menus["trash"]);
        }

        return array_values($menus);
    }

    /**
     * @Route("/", methods="POST")
     * @Request({"menu":"array"}, csrf=true)
     * @param $menu
     * @return array
     */
    #[ArrayShape(['message' => "string", 'menu' => ""])]
    public function saveAction($menu): array
    {
        $oldId = isset($menu["id"]) ? trim($menu["id"]) : null;
        $label = trim($menu["label"]);

        if (!($id = App::filter($label, "slugify"))) {
            App::jsonabort(400, __("Invalid id."));
        }

        if ($id != $oldId) {
            if ($this->config->has("menus." . $id)) {
                throw new ConflictException(__("Duplicate Menu Id."));
            }

            $this->config->remove("menus." . $oldId);

            Node::where(["menu = :old"], [":old" => $oldId])->update(["menu" => $id]);
        }

        $this->config->merge(["menus" => [$id => compact("id", "label")]]);

        App::menu()->assign($id, $menu["positions"]);

        return ["message" => "success", "menu" => $menu];
    }

    /**
     * @Route("/{id}", methods="DELETE")
     * @Request({"id"}, csrf=true)
     * @param $id
     * @return array
     */
    #[ArrayShape(['message' => "string"])]
    public function deleteAction($id): array
    {
        App::config("system/site")->remove("menus." . $id);
        Node::where(["menu = :id"], [":id" => $id])->update(["menu" => "trash", "status" => 0]);

        return ["message" => "success"];
    }
}
