<?php

namespace GreenCheap\Widget\Controller;

use GreenCheap\Application as App;
use GreenCheap\Routing\Annotation\Request;
use GreenCheap\Routing\Annotation\Route;
use GreenCheap\User\Annotation\Access;
use GreenCheap\Widget\Model\Widget;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @Access("system: manage widgets")
 */
class WidgetApiController
{
    /**
     * @Route("/", methods="GET")
     */
    #[ArrayShape(['positions' => "array", 'unassigned' => "\GreenCheap\Widget\Model\Widget[]"])]
    public function indexAction(): array
    {
        $widgets = Widget::findAll();
        $positions = App::position()->all();

        foreach ($positions as &$position) {
            $position["widgets"] = [];

            foreach ($position["assigned"] as $id) {
                if (isset($widgets[$id])) {
                    $position["widgets"][] = $widgets[$id];
                    unset($widgets[$id]);
                }
            }
        }

        return ["positions" => array_values($positions), "unassigned" => array_values($widgets)];
    }

    /**
     * @Route("/{id}", methods="GET", requirements={"id"="\d+"})
     * @param $id
     * @return mixed
     */
    public function getAction($id): mixed
    {
        if (!($widget = Widget::find($id))) {
            return App::jsonabort(404, "Widget not found.");
        }

        return $widget;
    }

    /**
     * @Request({"position", "ids": "array"}, csrf=true)
     * @param $position
     * @param $ids
     * @return array
     */
    #[ArrayShape(['message' => "string"])]
    public function assignAction($position, $ids): array
    {
        App::position()->assign($position, $ids);

        return ["message" => "success"];
    }

    /**
     * @Route("/", methods="POST")
     * @Route("/{id}", methods="POST", requirements={"id"="\d+"})
     * @Request({"widget": "array", "id": "int"}, csrf=true)
     * @param $data
     * @param int $id
     * @return mixed
     */
    public function saveAction($data, $id = 0): mixed
    {
        if (!$id) {
            $widget = Widget::create();
        } elseif (!($widget = Widget::find($id))) {
            return App::jsonabort(404, "Widget not found.");
        }

        if (empty($data["title"])) {
            return App::jsonabort(400, "Widget title empty.");
        }

        $widget->save($data);

        return ["message" => "success", "widget" => $widget, "data" => $data];
    }

    /**
     * @Route("/{id}", methods="DELETE", requirements={"id"="\d+"})
     * @Request({"id": "int"}, csrf=true)
     * @param $id
     * @return mixed
     */
    #[ArrayShape(['message' => "string"])]
    public function deleteAction($id): mixed
    {
        if (!($widget = Widget::find($id))) {
            App::jsonabort(404, "Widget not found.");
        }

        $widget->delete();

        return ["message" => "success"];
    }

    /**
     * @Route(methods="POST")
     * @Request({"ids": "int[]"}, csrf=true)
     * @param array $ids
     * @return array
     */
    #[ArrayShape(['message' => "string"])]
    public function copyAction($ids = []): array
    {
        foreach ($ids as $id) {
            if ($widget = Widget::find((int) $id)) {
                $copy = clone $widget;
                $copy->id = null;
                $copy->status = 0;
                $copy->title = $widget->title . " - " . __("Copy");
                $copy->save();
            }
        }

        return ["message" => "success"];
    }

    /**
     * @Route("/bulk", methods="POST")
     * @Request({"widgets": "array"}, csrf=true)
     * @param array $widgets
     * @return array
     */
    #[ArrayShape(['message' => "string"])]
    public function bulkSaveAction($widgets = []): array
    {
        foreach ($widgets as $data) {
            $this->saveAction($data, isset($data["id"]) ? $data["id"] : 0);
        }

        return ["message" => "success"];
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

        return ["message" => "success"];
    }
}
