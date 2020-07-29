<?php
namespace GreenCheap\Categories\Service;

use GreenCheap\Categories\Model\Categories;

class CategoriesService
{
    /**
     * @var Categories
     */
    protected $categories;

    /**
     * CategoriesService constructor.
     */
    public function __construct()
    {
        $this->categories = new Categories();
    }

    /**
     * @param string $type
     * @return
     */
    public function getAll( string $type = '' )
    {
        return $this->queryCategories($type);
    }

    /**
     * @param string $type
     * @param int $sub_category
     * @return
     */
    protected function queryCategories( string $type = '' , int $sub_category = 0 )
    {
        $data = [];
        $query = Categories::where(['type = :type', 'sub_category = :sub_category'], ['type' => $type , 'sub_category' => $sub_category])
            ->orderBy('title' , 'ASC')
            ->get();
        if( $query ){
            foreach ($query as $key => $category){
                if($category->hasSubCategory()){
                    $category->subcategories = $this->queryCategories($type , $category->id);
                }else{
                    $category->subcategories = [];
                }
            }
        }
        return $query;
    }
}
