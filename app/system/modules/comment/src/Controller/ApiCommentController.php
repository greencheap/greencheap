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
     * @param array $filter
     * @param int $page
     * @return array
     */
    public function indexAction(array $filter = [] , int $page = 0):array
    {
        $query = Comment::query();

        $filter = array_merge(array_fill_keys(['order', 'limit', 'parent_id', 'own_id', 'type', 'author', 'developer', 'status', 'search'], ''), $filter);
        extract($filter, EXTR_SKIP);

        if (!preg_match('/^(created|title)\s(asc|desc)$/i', $order, $order)) {
            $order = [1 => 'created', 2 => 'desc'];
        }

        if( (bool) !$developer){
            $query->where('parent_id = :parent_id' , [
                'parent_id' => (int) $parent_id ?? 0
            ]);
            $query->where('status = :status' , [
                'status' => Comment::STATUS_APPROVED
            ]);
        }else{
            if(is_numeric($status)){
                $query->where('status = :status' , [
                    'status' => (int) $status
                ]);
            }
            if ($search) {
                $query->where(function ($query) use ($search) {
                    $query->orWhere(['content LIKE :search'], ['search' => "%{$search}%"]);
                });
            }
        }

        if($author){
            $query->where('user_id = :user' , ['user' => $author]);
        }

        if($own_id){
            $query->where('own_id = :own_id' , compact('own_id'));
        }

        if($type){
            $query->where('type = :type' , compact('type'));
        }

        $limit = (int) $limit ?: $this->module->config('comments_per_page');
        $count = $query->count();
        $pages = ceil($count / $limit);
        $page  = max(0, min($pages - 1, $page));

        $comments = array_values($query->offset($page * $limit)->limit($limit)->orderBy($order[1], $order[2])->related('user')->get());

        return compact('comments', 'pages', 'count');
    }

    /**
     * @Request({"comment":"array","id":"integer"} , csrf=true)
     * @param array $comment
     * @param int $id
     * @return array|void
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
     * @Route(methods="DELETE")
     * @Request({"comment":"array"} , csrf=true)
     * @param array $comment
     * @return bool|void
     */
    public function deleteAction(array $comment = [])
    {
        $user = App::user();
        if(($user->hasPermission('comment: manage own remove') && $user->id == $comment['user_id']) || ($user->hasPermission('comment: manage all remove comment')) || $user->isAdministrator()){
            $query = Comment::find($comment['id']);
            $query->delete();
            return true;
        }
        return App::abort(403 , __('You do not have the authority to delete this comment.'));
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

    /**
     * @Route("/bulk", methods="POST")
     * @Request({"comments": "array"}, csrf=true)
     * @param array $comments
     */
    public function bulkSaveAction( array $comments = [] )
    {
        foreach ($comments as $data) {
            $this->saveAction($data, isset($data['id']) ? $data['id'] : 0);
        }

        return ['message' => 'success'];
    }

    /**
     * @Route("/bulk", methods="DELETE")
     * @Request({"comments": "array"}, csrf=true)
     * @return string[]
     */
    public function bulkDeleteAction( array $comments = [] )
    {
        foreach (array_filter($comments) as $comment) {
            $this->deleteAction($comment);
        }
        return ['message' => 'success'];
    }

    /**
     * @Access(admin=true)
     * @Route("sendinformation")
     * @Request({"comment":"array"} , csrf=true)
     */
    public function sendInformationAction(array $comment = [])
    {
        $message = __('Your comment has been approved by the administrator and is now live.');
        $name = $comment['author']['name'];
        $email = $comment['author']['email'];
        $commentInformation = $comment['content'];
        $mail = App::mailer()->create();
        $mail->setTo($email)
        ->setSubject(__('%site% - Your comment has been approved.', ['%site%' => App::module('system/site')->config('title')]))
        ->setBody(App::view('system/comment:mails/information.php', compact(['message' , 'name' , 'commentInformation'])), 'text/html')
        ->send();

        return ['message' => 'success'];
    }
}
?>
