<?php

namespace GreenCheap\User\Controller;

use GreenCheap\Application as App;
use GreenCheap\Routing\Annotation\Request;
use GreenCheap\Routing\Annotation\Route;
use GreenCheap\User\Annotation\Access;
use GreenCheap\User\Model\Role;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @Access("user: manage user permissions")
 */
class RoleApiController
{
    /**
     * @Route("/", methods="GET")
     */
    public function indexAction(): array
    {
        return array_values(Role::findAll());
    }

    /**
     * @Route("/{id}", methods="GET", requirements={"id"="\d+"})
     * @param $id
     * @return Role
     */
    public function getAction($id): Role
    {
        return Role::find($id);
    }

    /**
     * @Route("/", methods="POST")
     * @Route("/{id}", methods="POST", requirements={"id"="\d+"})
     * @Request({"role": "array", "id": "int"}, csrf=true)
     * @param $data
     * @param int $id
     * @return array
     */
    #[ArrayShape(['message' => "string", 'role' => "\GreenCheap\User\Model\Role"])]
    public function saveAction($data, $id = 0): mixed
    {
        // is new ?
        if (!$role = Role::find($id)) {

            if ($id) {
                return App::jsonabort(404, __('Role not found.'));
            }

            $role = Role::create();
        }

        $role->save($data);

        return ['message' => 'success', 'role' => $role];
    }

    /**
     * @Route("/{id}", methods="DELETE", requirements={"id"="\d+"})
     * @Request({"id": "int"}, csrf=true)
     * @param int $id
     * @return array
     */
    #[ArrayShape(['message' => "string"])]
    public function deleteAction($id = 0): array
    {
        if ($role = Role::find($id)) {
            $role->delete();
        }

        return ['message' => 'success'];
    }

    /**
     * @Route("/bulk", methods="POST")
     * @Request({"roles": "array"}, csrf=true)
     * @param array $roles
     * @return array
     */
    #[ArrayShape(['message' => "string"])]
    public function bulkSaveAction($roles = []): array
    {
        foreach ($roles as $data) {
            $this->saveAction($data, isset($data['id']) ? $data['id'] : 0);
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
