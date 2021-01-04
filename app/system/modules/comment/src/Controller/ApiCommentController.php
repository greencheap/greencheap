<?php 
namespace GreenCheap\Comment\Controller;

use GreenCheap\Application as App;
use GreenCheap\Comment\Model\Comment;
use GreenCheap\Comment\CommentPlugin;
use GreenCheap\User\Model\User;

class ApiCommentController
{
    /**
     * Comment Module
     */
    protected $module;

    /**
     * 
     */
    public function __construct()
    {
        $this->module = App::module('system/comment');
    }

    /**
     * @Route(methods="GET")
     * @Request({"filter":"array","page":"integer"} , csrf=true)
     */
    public function indexAction(array $filter = [] , int $page = 0):array
    {
        $query = Comment::query();
        
        $filter = array_merge(array_fill_keys(['order', 'limit', 'parent_id', 'own_id', 'type'], ''), $filter);
        extract($filter, EXTR_SKIP);

        if (!preg_match('/^(date|title)\s(asc|desc)$/i', $order, $order)) {
            $order = [1 => 'created', 2 => 'desc'];
        }
        
        $query->where('parent_id = :parent_id' , [
            'parent_id' => (int) $parent_id ?? 0
        ]);

        $query->where('status = :status' , [
            'status' => Comment::STATUS_APPROVED
        ]);

        $query->where([
            'own_id = :own_id',
            'type = :type'
        ], [
            'own_id' => $own_id,
            'type' => $type
        ]);

        $limit = (int) $this->module->config('comments_per_page');
        $count = $query->count();
        $pages = ceil($count / $limit);
        $page  = max(0, min($pages - 1, $page));

        $comments = array_values($query->offset($page * $limit)->limit($limit)->orderBy($order[1], $order[2])->related('user')->get());

        return compact('comments', 'pages', 'count');
    }

    /**
     * @Request({"comment":"array","id":"integer"} , csrf=true)
     */
    public function saveAction(array $comment = [] , int $id = 0)
    {
        if( !$query = Comment::where(compact('id'))->first() ){
            if($id){
                return App::abort(404 , __('Not Found Comment'));
            }
            $query = Comment::create();
        }
        $comment['created'] = new \DateTime();
        $comment['content'] = CommentPlugin::onContentPlugins($comment['content']);

        if($comment['user_id'] && Comment::isInterpretationThreshold($comment, $this->module->config('threshold_comment'))){
            return App::abort(429 , __('Too Many Requests For Comment'));
        }

        $query->save($comment);
        return compact('query');
    }

    /**
     * @Request(csrf=true)
     */
    public function getUsersAction():array
    {
        $db = App::db();
        $users = $db->createQueryBuilder()
        ->select(['username as value' , 'username as label' , 'email'])
        ->from('@system_user')
        ->where('status = ?' , [User::STATUS_ACTIVE])
        ->where('id != ?' , [App::user()->id])
        ->get();
        return compact('users');
    }
}
?>