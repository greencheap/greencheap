<?php
namespace GreenCheap\Categories\Controller;

use GreenCheap\Application as App;
use GreenCheap\System\Model\StatusModelTrait;
use GreenCheap\User\Model\User;
use GreenCheap\Categories\Model\Categories;

/**
 * @Access(admin=true)
 * @Route(name="admin")
 */
class CategoriesController
{
    /**
     * @Route("/")
     * @param array $filters
     * @param int $page
     * @return array[]
     */
    public function indexAction(array $filters = [] , int $page = 1)
    {
        return [
            '$view' => [
                'title' => __('Categories'),
                'name'  => 'system/categories:views/admin/index.php'
            ],
            '$data' => [
                'config' => [
                    'filters' => (object) array_merge($filters , [
                        'type' => 'blog'
                    ]),
                    'page' => $page,
                ],
                'statuses' => StatusModelTrait::getStatuses(),
                'user' => App::user()
            ]
        ];
    }

    /**
     * @param int $id
     * @param string $redirect
     * @param string|null $type
     * @return array
     * @Route("/edit")
     * @Request({"id":"int", "redirect":"string", "type":"string"})
     */
    public function editAction(int $id = 0, string $redirect = null, string $type = null)
    {
        $category = '';

        return [
            '$view' => [
                'title' => 'Başlık',
                'name' => 'system/categories:views/admin/edit.php'
            ],
            '$data' => [
                'category' => $category,
                'users' => User::findAll(),
                'statuses' => StatusModelTrait::getStatuses(),
                'categories' => Categories::findAll(),
            ]
        ];
    }
}
?>
