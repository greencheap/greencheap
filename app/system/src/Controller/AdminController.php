<?php

namespace GreenCheap\System\Controller;

use GreenCheap\Application as App;
use GreenCheap\Auth\Auth;
use GreenCheap\Routing\Annotation\Request;
use GreenCheap\Routing\Annotation\Route;
use GreenCheap\User\Annotation\Access;
use GreenCheap\User\Model\User;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class AdminController
 * @package GreenCheap\System\Controller
 */
class AdminController
{
    /**
     * @Access(admin=true)
     */
    public function indexAction()
    {
        return App::redirect("@dashboard");
    }

    /**
     * @Route("/admin/login", defaults={"_maintenance"=true})
     * @Request({"redirect": "string", "message": "string"})
     * @param string $redirect
     * @param string $message
     */
    public function loginAction($redirect = "", $message = "")
    {
        if (App::user()->isAuthenticated()) {
            return App::redirect("@system");
        }

        return [
            '$view' => [
                "title" => __("Login"),
                "name" => "system/theme:views/login.php",
                "layout" => false,
            ],
            "last_username" => App::session()->get(Auth::LAST_USERNAME),
            "redirect" => $redirect ?: App::url("@system"),
            "message" => $message,
            "images" => [self::getUnsplashImages()[rand(0, 2)]],
        ];
    }

    /**
     * @Access(admin=true)
     * @Request({"order": "array"})
     * @param $order
     * @return mixed
     */
    #[ArrayShape(['message' => "mixed"])]
    public function adminMenuAction($order): mixed
    {
        if (!$order) {
            return App::abort(400, __("Missing order data."));
        }

        $user = User::find(App::user()->id);
        $user->set("admin.menu", $order);
        $user->save();

        return ["message" => __("Order saved.")];
    }

    /**
     * @return array[]
     */
    public static function getUnsplashImages(): array
    {
        return [
            [
                "image" => "https://images.unsplash.com/photo-1539104389789-1d1a7a54dc73?ixlib=rb-1.2.1&ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&auto=format&fit=crop&w=1000&q=80",
                "information" => [
                    "locationName" => "Aspen, United States",
                    "label" => "Clouds over the mountain.",
                ],
                "author" => [
                    "fullName" => "Josh Hild",
                    "userName" => "@joshhild",
                    "profileUrl" => "https://unsplash.com/@joshhild",
                    "avatarUrl" => "https://images.unsplash.com/profile-1534444770498-421ec361dce5?dpr=1&auto=format&fit=crop&w=32&h=32&q=60&crop=faces&bg=fff",
                ],
            ],
            [
                "image" => "https://images.unsplash.com/photo-1526837892957-feb285737692?ixlib=rb-1.2.1&ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&auto=format&fit=crop&w=1000&q=80",
                "information" => [
                    "locationName" => "Yosemite Valley, United States",
                    "label" => "Focal point",
                ],
                "author" => [
                    "fullName" => "Casey Horner",
                    "userName" => "@mischievous_penguins",
                    "profileUrl" => "https://unsplash.com/@mischievous_penguins",
                    "avatarUrl" => "https://images.unsplash.com/profile-1502669002421-a8d274ad2897?dpr=1&auto=format&fit=crop&w=32&h=32&q=60&crop=faces&bg=fff",
                ],
            ],
            [
                "image" => "https://images.unsplash.com/photo-1489363855452-7327672b1608?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80",
                "information" => [
                    "locationName" => "Canadian Rockies, Canada",
                    "label" => "I was on the road home from Revelstoke and it was a beautiful winters afternoon",
                ],
                "author" => [
                    "fullName" => "Joshua Reddekopp",
                    "userName" => "@joshuaryanphoto",
                    "profileUrl" => "https://unsplash.com/@joshuaryanphoto",
                    "avatarUrl" => "https://images.unsplash.com/profile-1589066579465-8520f6c99df5image?dpr=1&auto=format&fit=crop&w=32&h=32&q=60&crop=faces&bg=fff",
                ],
            ],
        ];
    }
}
