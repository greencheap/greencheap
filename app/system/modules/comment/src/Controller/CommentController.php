<?php

namespace GreenCheap\Comment\Controller;

use GreenCheap\Application as App;
use GreenCheap\User\Model\User;
/**
 * @Access(admin=true)
 */
class CommentController
{
    /**
     * @Acces("comment: access comment")
     * @Route("/page" , name="page")
     * @Request({"filter":"array", "page":"int"})
     */
    public function indexAction(array $filter = [] , int $page = 0): array
    {
        $db = App::db();
        $roles = $db->createQueryBuilder()
        ->from('@system_role')
        ->get();

        return [
            '$view' => [
                'title' => __('Comments'),
                'name' => 'system:modules/comment/views/admin/index.php'
            ],
            '$data' => [
                'authors'  => User::findAll(),
                'canEditAll' => App::user()->hasAccess('comment: manage all comment'),
                'config' => [
                    'filter' => (object) $filter,
                    'page' => $page
                ]
            ]
        ];
    }

    /**
     * @Access("comment: manage settings")
     * @Route("/settings" , name="settings")
     */
    public function settingsAction():array
    {
        $module = App::module('system/comment');
        return [
            '$view' => [
                'title' => __('Settings'),
                'name' => 'system:modules/comment/views/admin/settings.php'
            ],
            '$data' => [
                'config' => $module->get('config')
            ]
        ];
    }
}

?>