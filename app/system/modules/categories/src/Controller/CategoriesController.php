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

        $util = App::db();
        $categories_type = $util->createQueryBuilder()->select('type')->from('@system_categories')->groupBy('type')->get();
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
                'user' => App::user(),
                'currentUrl' => App::url()->current(),
                'types' => $categories_type
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
        if(!$type){
            return App::abort(404 , __('Can Not Find Type'));
        }

        $category = Categories::where(compact(['id' , 'type']))->first();
        if(!$category){
            if($id){
                return App::abort(404 , __('Not Found Category'));
            }

            $category = Categories::create([
                'type' => $type,
                'sub_category' => 0,
                'date' => new \DateTime(),
                'user_id' => App::user()->id,
                'status' => StatusModelTrait::getStatus('STATUS_PUBLISHED')
            ]);
        }

        $datetime = new \DateTime();

        return [
            '$view' => [
                'title' => $category->id ? __('Edit %category_name%' , ['category_name' => $category->title]) : __('New Category'),
                'name' => 'system/categories:views/admin/edit.php'
            ],
            '$data' => [
                'category' => (object) $category,
                'data' => [
                    'users' => User::findAll(),
                    'statuses' => StatusModelTrait::getStatuses(),
                    'categories' => Categories::findAll(),
                    'date' => $datetime,
                    'redirect' => $redirect
                ]
            ]
        ];
    }
}
?>
