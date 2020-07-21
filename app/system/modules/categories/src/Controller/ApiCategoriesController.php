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
        $filters = array_merge(array_fill_keys(['status', 'type' , 'search', 'author', 'order', 'limit'], ''), $filters);
        extract($filters, EXTR_SKIP);

        if(!App::user()->hasAccess('categories: manage categories')) {
            $author = App::user()->id;
        }

        $query->where(['type' => (string) $type]);

        if (is_numeric($status)) {
            $query->where(['status' => (int) $status]);
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
}
