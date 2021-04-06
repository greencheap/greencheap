<?php

namespace GreenCheap\User\Controller;

use GreenCheap\Application as App;
use GreenCheap\Routing\Annotation\Request;
use GreenCheap\User\Annotation\Access;
use GreenCheap\User\Model\Role;
use GreenCheap\User\Model\User;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @Access(admin=true)
 */
class UserController
{
    /**
     * @Access("user: manage users")
     * @Request({"filter": "array", "page":"int"})
     * @param array $filter
     * @param null $page
     * @return array
     */
    #[ArrayShape(['$view' => "array", '$data' => "array[]"])]
    public function indexAction($filter = [], $page = null): array
    {
        $roles = $this->getRoles();
        unset($roles[Role::ROLE_AUTHENTICATED]);

        return [
            '$view' => [
                "title" => __("Users"),
                "name" => "system/user/admin/user-index.php",
            ],
            '$data' => [
                "config" => [
                    "statuses" => User::getStatuses(),
                    "roles" => array_values($roles),
                    "emailVerification" => App::module("system/user")->config("require_verification"),
                    "filter" => (object) $filter,
                    "page" => $page,
                ],
            ],
        ];
    }

    /**
     * @Access("user: manage users")
     * @Request({"id": "int"})
     * @param int $id
     * @return mixed
     */
    #[ArrayShape(['$view' => "array", '$data' => "array"])]
    public function editAction($id = 0): mixed
    {
        if (!$id) {
            $user = User::create(["roles" => [Role::ROLE_AUTHENTICATED]]);
        } elseif (!($user = User::find($id))) {
            App::abort(404, "User not found.");
        }

        return [
            '$view' => [
                "title" => $id ? __("Edit User") : __("Add User"),
                "name" => "system/user/admin/user-edit.php",
            ],
            '$data' => [
                "user" => $user,
                "config" => [
                    "statuses" => User::getStatuses(),
                    "roles" => array_values($this->getRoles($user)),
                    "emailVerification" => App::module("system/user")->config("require_verification"),
                    "currentUser" => App::user()->id,
                ],
            ],
        ];
    }

    /**
     * @Access("user: manage user permissions")
     */
    #[ArrayShape(['$view' => "array", '$data' => "array"])]
    public function permissionsAction(): array
    {
        return [
            '$view' => [
                "title" => __("Permissions"),
                "name" => "system/user/admin/permission-index.php",
            ],
            '$data' => [
                "permissions" => App::module("system/user")->getPermissions(),
                "roles" => array_values(
                    Role::query()
                        ->orderBy("priority")
                        ->get()
                ),
            ],
        ];
    }

    /**
     * @Access("user: manage user permissions")
     * @Request({"id": "int"})
     * @param null $id
     * @return array
     */
    #[ArrayShape(['$view' => "array", '$config' => "array|null[]", '$data' => "array"])]
    public function rolesAction($id = null): array
    {
        return [
            '$view' => [
                "title" => __("Roles"),
                "name" => "system/user/admin/role-index.php",
            ],
            '$config' => [
                "role" => $id,
            ],
            '$data' => [
                "permissions" => App::module("system/user")->getPermissions(),
                "roles" => array_values(
                    Role::query()
                        ->orderBy("priority")
                        ->get()
                ),
            ],
        ];
    }

    /**
     * @Access("system: access settings")
     */
    #[ArrayShape(['$view' => "array", '$data' => "array"])]
    public function settingsAction(): array
    {
        return [
            '$view' => [
                "title" => __("User Settings"),
                "name" => "system/user/admin/settings.php",
            ],
            '$data' => [
                "config" => App::module("system/user")->config(),
            ],
        ];
    }

    /**
     * Gets the user roles.
     *
     * @param User|null $user
     * @return array
     */
    protected function getRoles(User $user = null): array
    {
        $roles = [];
        $self = $user && $user->id === App::user()->id;
        foreach (
            Role::where(["id <> ?"], [Role::ROLE_ANONYMOUS])
                ->orderBy("priority")
                ->get()
            as $role
        ) {
            $r = $role->jsonSerialize();

            if ($role->isAuthenticated()) {
                $r["disabled"] = true;
            }

            if ($user && $role->isAdministrator() && (!App::user()->isAdministrator() || $self)) {
                $r["disabled"] = true;
            }

            $roles[$r["id"]] = $r;
        }

        return $roles;
    }
}
