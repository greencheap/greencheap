<?php

namespace GreenCheap\User\Controller;

use GreenCheap\Application as App;
use GreenCheap\Application\Exception;
use GreenCheap\Routing\Annotation\Request;
use GreenCheap\Routing\Annotation\Route;
use GreenCheap\User\Annotation\Access;
use GreenCheap\User\Model\Role;
use GreenCheap\User\Model\User;
use JetBrains\PhpStorm\ArrayShape;
use RandomLib\Factory;

/**
 * @Access("user: manage users")
 */
class UserApiController
{
    /**
     * @Route("/", methods="GET")
     * @Request({"filter": "array", "page":"int", "limit":"int"})
     * @param array $filter
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function indexAction($filter = [], $page = 0, $limit = 0): array
    {
        $query  = User::query();
        $filter = array_merge(array_fill_keys(['status', 'search', 'role', 'order', 'access'], ''), $filter);
        extract($filter, EXTR_SKIP);

        if (is_numeric($status)) {

            $query->where(['status' => (int) $status]);

            if ($status) {
                $query->where('login IS NOT NULL');
            }

        } elseif ('new' == $status) {
            $query->where(['status' => User::STATUS_ACTIVE, 'login IS NULL']);
        }

        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->orWhere(['username LIKE :search', 'name LIKE :search', 'email LIKE :search'], ['search' => "%{$search}%"]);
            });
        }

        if ($role) {
            $query->where(function($query)use($role){
                $query->whereInSet('roles', (int) $role);
            });
        }

        if ($access) {
            $query->whereExists(function($query) use ($access) {
                $query
                    ->select('id')->from('@system_auth as a')
                    ->where('a.user_id = @system_user.id')
                    ->where(['a.access > :access', 'a.status > :status'], ['access' => date('Y-m-d H:i:s', time() - max(0, (int) $access)), 'status' => 0]);
            });
        }

        if (preg_match('/^(username|name|email|registered|login)\s(asc|desc)$/i', $order, $match)) {
            $order = $match;
        } else {
            $order = [1=>'username', 2=>'asc'];
        }

        $default = App::module('system/user')->config('users_per_page');
        $limit   = min(max(0, $limit), $default) ?: $default;
        $count   = $query->count();
        $pages   = ceil($count / $limit);
        $page    = max(0, min($pages - 1, $page));
        $users   = array_values($query->offset($page * $limit)->limit($limit)->orderBy($order[1], $order[2])->get());

        return compact('users', 'pages', 'count');
    }

    /**
     * @Request({"filter": "array"})
     * @param array $filter
     * @return array
     */
    public function countAction($filter = []): array
    {
        $query  = User::query();
        $filter = array_merge(array_fill_keys(['status', 'search', 'role', 'order', 'access'], ''), (array)$filter);
        extract($filter, EXTR_SKIP);

        if (is_numeric($status)) {

            $query->where(['status' => (int) $status]);

            if ($status) {
                $query->where('login IS NOT NULL');
            }

        } elseif ('new' == $status) {
            $query->where(['status' => User::STATUS_ACTIVE, 'login IS NULL']);
        }

        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->orWhere(['username LIKE :search', 'name LIKE :search', 'email LIKE :search'], ['search' => "%{$search}%"]);
            });
        }

        if ($role) {
            $query->whereInSet('roles', $role);
        }

        if ($access) {
            $query->whereExists(function($query) use ($access) {
                $query
                    ->select('id')->from('@system_auth as a')
                    ->where('a.user_id = @system_user.id')
                    ->where(['a.access > :access', 'a.status > :status'], ['access' => date('Y-m-d H:i:s', time() - max(0, (int) $access)), 'status' => 0]);
            });
        }

        $count = $query->count();

        return compact('count');
    }

    /**
     * @Route("/{id}", methods="GET", requirements={"id"="\d+"})
     * @param $id
     * @return User
     */
    public function getAction($id): User|null
    {
        if (!$user = User::find($id)) {
            return App::abort(404, 'User not found.');
        }

        return $user;
    }

    /**
     * @Route("/", methods="POST")
     * @Route("/{id}", methods="POST", requirements={"id"="\d+"})
     * @Request({"user": "array", "password", "id": "int"}, csrf=true)
     * @param $data
     * @param null $password
     * @param int $id
     * @return array
     */
    #[ArrayShape(['message' => "string", 'user' => "\GreenCheap\User\Model\User"])]
    public function saveAction($data, $password = null, $id = 0): array
    {
        try {

            // is new ?
            if (!$user = User::find($id)) {

                if ($id) {
                    App::abort(404, __('User not found.'));
                }

                if (!$password) {
                    App::abort(400, __('Password required.'));
                }

                $user = User::create(['registered' => new \DateTime]);
            }

            if ($user->isAdministrator() && !App::user()->isAdministrator()) {
                App::abort(400, __('Unable to edit administrator.'));
            }

            $user->name = @$data['name'];
            $user->username = @$data['username'];
            $user->email = @$data['email'];

            $self = App::user()->id == $user->id;
            if ($self && @$data['status'] == User::STATUS_BLOCKED) {
                App::abort(400, __('Unable to block yourself.'));
            }

            if (@$data['email'] != $user->email) {
                $user->set('verified', false);
            }

            if (!empty($password)) {

                if (trim($password) != $password || strlen($password) < 3) {
                    throw new Exception(__('Invalid Password.'));
                }

                $user->password = App::get('auth.password')->hash($password);
            }

            $key    = array_search(Role::ROLE_ADMINISTRATOR, @$data['roles'] ?: []);
            $add    = false !== $key && !$user->isAdministrator();
            $remove = false === $key && $user->isAdministrator();

            if (($self && $remove) || !App::user()->isAdministrator() && ($remove || $add)) {
                App::abort(403, 'Cannot add/remove Admin Role.');
            }

            unset($data['login'], $data['registered']);

            $user->validate();
            $user->save($data);

            return ['message' => 'success', 'user' => $user];

        } catch (Exception $e) {
            App::abort(400, $e->getMessage());
        }
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
        if (App::user()->id == $id) {
            App::abort(400, __('Unable to delete yourself.'));
        }

        if ($user = User::find($id)) {
            if ($user->isAdministrator() && !App::user()->isAdministrator()) {
                App::abort(400, __('Unable to delete administrator.'));
            }

            $user->delete();
        }

        return ['message' => 'success'];
    }

    /**
     * @Route("/avatar-upload" , methods="POST")
     * @Request(csrf=true)
     * @param Request $request
     * @return array
     */
    public function avatarUploadAction(Request $request): array
    {
        $file = $request->files->get('_avatar');
        if( !App::file()->exists(App::get('path').'/storage/users/avatar') ){
            App::file()->makeDir(App::get('path').'/storage/users/avatar');
        }
        $factory = new Factory();
        $generator = $factory->getMediumStrengthGenerator();
        $bytes = $generator->generateString(50, 'qwertyuioplkjhgfdsazxcvbnm');
        $file->move(App::get('path').'/storage/users/avatar/' , $bytes.'.'.$file->guessClientExtension());
        $path = 'storage/users/avatar/'.$bytes.'.'.$file->guessClientExtension();
        return compact('path');
    }

    /**
     * @Route("/bulk", methods="POST")
     * @Request({"users": "array"}, csrf=true)
     * @param array $users
     * @return array
     */
    #[ArrayShape(['message' => "string"])]
    public function bulkSaveAction($users = []): array
    {
        foreach ($users as $data) {
            $this->saveAction($data, null, isset($data['id']) ? $data['id'] : 0);
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
}
