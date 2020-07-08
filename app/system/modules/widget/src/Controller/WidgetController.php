<?php

namespace GreenCheap\Widget\Controller;

use GreenCheap\Application as App;
use GreenCheap\Site\Model\Node;
use GreenCheap\User\Model\Role;
use GreenCheap\Widget\Model\Widget;

/**
 * @Access("system: manage widgets", admin=true)
 */
class WidgetController
{
    public function indexAction()
    {
        return [
            '$view' => [
                'title' => __('Widgets'),
                'name' => 'system/widget/index.php'
            ],
            '$data' => [
                'widgets' => array_values(Widget::findAll()),
                'types' => App::widget()->all(),
                'config' => [
                    'menus' => App::menu(),
                    'nodes' => array_values(Node::query()->get())
                ]
            ]
        ];
    }

    /**
     * @Request({"id": "int", "type": "string"})
     */
    public function editAction($id = 0, $type = null)
    {
        if (!$id) {
            $widget = Widget::create(['type' => $type]);
        } else if (!$widget = Widget::find($id)) {
            App::abort(404, 'Widget not found.');
        }

        return [
            '$view' => [
                'title' => __('Widgets'),
                'name' => 'system/widget/edit.php'
            ],
            '$data' => [
                'widget' => $widget,
                'config' => [
                    'menus' => App::menu(),
                    'nodes' => array_values(Node::query()->get()),
                    'roles' => array_values(Role::findAll()),
                    'types' => array_values(App::widget()->all()),
                    'positions' => array_values(App::position()->all())
                ]
            ]
        ];
    }
}
