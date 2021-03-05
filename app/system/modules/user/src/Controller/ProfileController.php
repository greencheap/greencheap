<?php

namespace GreenCheap\User\Controller;

use GreenCheap\Application as App;
use GreenCheap\Application\Exception;
use GreenCheap\Routing\Annotation\Request;
use GreenCheap\User\Model\User;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class ProfileController
 * @package GreenCheap\User\Controller
 */
class ProfileController
{
    /**
     * @return mixed
     */
    public function indexAction(): mixed
    {
        $user = App::user();

        if (!$user->isAuthenticated()) {
            return App::redirect('@user/login', ['redirect' => App::url()->current()]);
        }

        return [
            '$view' => [
                'title' => __('Profile'),
                'name'  => 'system/user/profile.php'
            ],
            '$data' => [
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email
                ]
            ]
        ];
    }

    /**
     * @Request({"user": "array"}, csrf=true)
     * @param $data
     * @return array
     */
    #[ArrayShape(['message' => "string"])]
    public function saveAction($data): mixed
    {
        $user = App::user();

        if (!$user->isAuthenticated()) {
            return App::jsonabort(404);
        }

        try {

            $user = User::find($user->id);

            if ($password = @$data['password_new']) {

                if (!App::auth()->getUserProvider()->validateCredentials($user, ['password' => @$data['password_old']])) {
                    throw new Exception(__('Invalid Password.'));
                }

                if (trim($password) != $password || strlen($password) < 3) {
                    throw new Exception(__('Invalid Password.'));
                }

                $user->password = App::get('auth.password')->hash($password);
            }

            if (@$data['email'] != $user->email) {
                $user->set('verified', false);
            }

            $user->name = @$data['name'];
            $user->email = @$data['email'];

            $user->validate();
            $user->save();

            return ['message' => 'success'];

        } catch (Exception $e) {
            App::abort(400, $e->getMessage());
        }
    }
}
