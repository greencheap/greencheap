<?php

namespace GreenCheap\Comment\Controller;

use GreenCheap\Application as App;
use GreenCheap\Routing\Annotation\Request;
use GreenCheap\Routing\Annotation\Route;
use GreenCheap\User\Annotation\Access;
use GreenCheap\User\Model\User;
use GreenCheap\Comment\Model\Comment;

/**
 * @Access(admin=true)
 */
class CommentController
{
    /**
     * @Access("comment: access comment")
     * @Route("/page" , name="page")
     * @Request({"filter":"array", "page":"int"})
     * @param array $filter
     * @param int $page
     * @return array
     */
    public function indexAction(array $filter = [], int $page = 0): array
    {
        $db = App::db();

        $roles = $db
            ->createQueryBuilder()
            ->from("@system_role")
            ->get();

        $types = $db
            ->createQueryBuilder()
            ->select("type")
            ->from("@system_comments")
            ->groupBy("type")
            ->get();

        return [
            '$view' => [
                "title" => __("Comments"),
                "name" => "system:modules/comment/views/admin/index.php",
            ],
            '$data' => [
                "authors" => User::findAll(),
                "canEditAll" => App::user()->hasAccess("comment: manage all comment"),
                "config" => [
                    "filter" => (object) $filter,
                    "page" => $page,
                ],
                "statuses" => Comment::getStatuses(),
                "types" => $types,
            ],
        ];
    }

    /**
     * @Route("page/edit/{id}", name="page/edit", requirements={"id"="\d+"})
     * @Access("comment: manage all comment")
     * @param int $id
     * @return array
     */
    public function editAction(int $id = 0): array
    {
        if (
            !($comment = Comment::where(compact("id"))
                ->related("user")
                ->first())
        ) {
            return App::abort(404, __("Not Found Comment"));
        }

        $module = App::module("system/comment");

        return [
            '$view' => [
                "title" => __("Edit Comment"),
                "name" => "system:modules/comment/views/admin/edit.php",
            ],
            '$data' => [
                "comment" => $comment,
                "statuses" => Comment::getStatuses(),
                "originstatus" => $comment->status,
                "notify_reply" => $module->get("config.notify_reply"),
            ],
        ];
    }

    /**
     * @Access("comment: manage settings")
     * @Route("/settings" , name="settings")
     */
    public function settingsAction(): array
    {
        $module = App::module("system/comment");
        return [
            '$view' => [
                "title" => __("Settings"),
                "name" => "system:modules/comment/views/admin/settings.php",
            ],
            '$data' => [
                "config" => $module->get("config"),
            ],
        ];
    }
}

?>
