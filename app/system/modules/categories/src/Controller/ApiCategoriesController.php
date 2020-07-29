<?php
namespace GreenCheap\Categories\Controller;

use GreenCheap\Application as App;
use GreenCheap\Categories\Model\Categories;

/**
 * Class ApiCategories
 * @package GreenCheap\Categories\Controller
 * @Access(admin=true)
 */
class ApiCategoriesController
{
    /**
     * @Route("/get")
     * @Request({"filters":"array", "page":"integer"} , csrF=true)
     * @param array $filters
     * @param int $page
     * @return string
     */
    public function getAction( array $filters = [] , int $page = 1 )
    {
        $query = Categories::query();
        $filters = array_merge(array_fill_keys(['status', 'type' , 'search', 'author', 'order', 'limit' , 'sub_category'], ''), $filters);
        extract($filters, EXTR_SKIP);

        if(!App::user()->hasAccess('categories: manage categories')) {
            $author = App::user()->id;
        }

        if($type){
            $query->where(['type' => (string) $type]);
        }

        if (is_numeric($status)) {
            $query->where(['status' => (int) $status]);
        }

        if(is_numeric($sub_category)){
            $query->where('sub_category = :sub_category' , ['sub_category' => $sub_category]);
        }

        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->orWhere(['title LIKE :search', 'slug LIKE :search'], ['search' => "%{$search}%"]);
            });
        }

        if ($author) {
            $query->where(function ($query) use ($author) {
                $query->orWhere(['user_id' => (int) $author]);
            });
        }

        if (!preg_match('/^(date|title)\s(asc|desc)$/i', $order, $order)) {
            $order = [1 => 'date', 2 => 'desc'];
        }

        $limit = (int) $limit ?: App::module('system/categories')->config('categories.categories_per_page');
        $count = $query->count();
        $pages = ceil($count / $limit);
        $page  = max(0, min($pages - 1, $page));

        $categories = array_values($query->offset($page * $limit)->related('user')->limit($limit)->orderBy($order[1], $order[2])->get());

        return compact('categories', 'pages', 'count');
    }

    /**
     * @Route("/save" , methods="POST")
     * @Request({"id":"integer","data":"array"} , csrf=true)
     * @param int $id
     * @param array $data
     * @return string
     */
    public function saveAction( int $id = 0 , array $data = [] )
    {
        if (!$query = Categories::where('id')->first()) {
            if ($id) {
                return App::abort(404, __('Not Found %name%', ['%name%' => __('Category')]));
            }
            $query = Categories::create();
        }
        if(empty($data['slug'])){
            $data['slug'] = App::filter( !empty($data['slug']) ? $data['slug']:$data['title'] , 'slugify');
        }
        $query->save($data);
        return compact('query');
    }

    /**
     * @Route("/bulk", methods="DELETE")
     * @Request({"ids": "array"}, csrf=true)
     * @param array $ids
     * @return string[]
     */
    public function bulkDeleteAction($ids = [])
    {
        foreach (array_filter($ids) as $id) {
            $this->deleteAction($id);
        }

        return ['message' => 'success'];
    }

    /**
     * @param string $type
     * @param int $depth
     * @return array
     * @Route("/get-option")
     * @Request({"type":"string", "depth":"integer"} , csrf=true)
     */
    public function getOptionAction(string $type = '' , int $depth = 0)
    {
        $categories = Categories::where(['type = ?' , 'sub_category = ?'] , [$type , $depth])->orderBy('title' , 'ASC')->get();
        return compact('categories');
    }

    /**
     * @Route("/{id}", methods="DELETE", requirements={"id"="\d+"})
     * @Request({"id": "int"}, csrf=true)
     * @param $id
     * @return string[]
     */
    public function deleteAction($id)
    {
        if ($category = Categories::find($id)) {
            $category->delete();
        }

        return ['message' => 'success'];
    }
}
