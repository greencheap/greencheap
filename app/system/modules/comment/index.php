<?php

use GreenCheap\Comment\Model\Comment;
use GreenCheap\Comment\Events\CommentListener;
use GreenCheap\Comment\Events\MentionListener;

return [
    "name" => "system/comment",

    "main" => function ($app) {
        $app["comment.draft"] = new Comment();
        $app["comment.config"] = (object) array_merge((array) $this->get("config"), [
            "statuses" => Comment::getStatuses(),
        ]);
    },

    "autoload" => [
        "GreenCheap\\Comment\\" => "src",
    ],

    "routes" => [
        "/comment" => [
            "name" => "@comment",
            "controller" => "GreenCheap\\Comment\\Controller\\CommentController",
        ],
        "/api/comment" => [
            "name" => "@api/comment",
            "controller" => "GreenCheap\\Comment\\Controller\\ApiCommentController",
        ],
    ],

    "menu" => [
        "comment" => [
            "label" => "Comments",
            "url" => "@comment/page",
            "access" => "comment: access comment",
            "icon" => "system/comment:icon.svg",
            "priority" => 120,
        ],
        "comment: index" => [
            "parent" => "comment",
            "label" => "Comments",
            "url" => "@comment/page",
            "active" => "@comment/page*",
        ],
        "comment: settings" => [
            "parent" => "comment",
            "label" => "Settings",
            "url" => "@comment/settings",
            "active" => "@comment/settings",
            "access" => "comment: manage settings",
        ],
    ],

    "resources" => [
        "system/comment:" => "",
    ],

    "config" => [
        //The user will be notified when there is a reply.
        "notify_reply" => false,
        //Admin approved comment
        "approved_admin" => true,
        //Threshold of repeated comment
        "threshold_comment" => "-1 min",
        "markdown_enabled" => true,
        "attribute_people" => false,
        "to_quote" => true,
        "comments_per_page" => 999,
    ],

    "permissions" => [
        "comment: manage own remove" => [
            "title" => 'It\'s can remove its own comments.',
        ],
        "comment: manage all comment" => [
            "title" => "Can edit all comments.",
        ],
        "comment: manage all remove comment" => [
            "title" => "Can remove all comments",
        ],
        "comment: access comment" => [
            "title" => "You can access the comment field and view comments.",
        ],
        "comment: manage settings" => [
            "title" => "You can access the settings of the comment module.",
        ],
        "comment: write comment" => [
            "title" => "Users with this authority can comment.",
        ],
    ],

    "events" => [
        "boot" => function ($event, $app) {
            $app->subscribe(new CommentListener($this->config), new MentionListener($this->config));
        },
    ],
];
