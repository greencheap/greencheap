<?php

namespace GreenCheap\Notifications\Controller;

use GreenCheap\Application as App;
use GreenCheap\Notifications\Model\Notifications;
use GreenCheap\Routing\Annotation\Request;
use GreenCheap\User\Annotation\Access;

/**
 * Class ApiNotifications
 * @package GreenCheap\Notifications\Controller
 */
class ApiNotifications
{
    /**
     * @var
     */
    protected $user;

    /**
     * ApiNotifications constructor.
     */
    public function __construct()
    {
        $this->user = App::user();
    }

    /**
     * @Route("/get" , name="get")
     * @Request(csrf=true)
     */
    public function indexAction()
    {
        if($this->user->isAnonymous()){
            return App::abort(401 , __('Unauthorized'));
        }

        $id = $this->user->id;
        $permission = $this->user->roles;

        $query = Notifications::query();
        /**
         * $query->where(function($query) use ($permission){
        $query->whereIn('except_group' , $permission , true);
        });
        $query->where(function($query) use ($id){
        $query->whereIn('except_user' , $id , true);
        });
         */
        $query->orderBy('id' , 'desc')->limit(10);
        $notifications = $query->get();

        return compact('notifications');
    }

    /**
     * @Route(methods="POST")
     * @Request({"notification":"array"} , csrf=true)
     * @param array $notification
     * @return array|void
     */
    public function readAction( array $notification )
    {
        $id = $notification['id'];
        if( !$query = Notifications::where(compact('id'))->first() ){
            return App::abort(404 , __('Not Found Notification'));
        }
        if(!is_array($query->read_user && !$query->read_user)){
            $query->read_user = [];
        }
        $query->except_group = array_values($query->except_group);
        $query->except_user = array_values($query->except_user);
        $query->read_group = array_values($query->read_group);
        $query->read_user = array_values(array_merge($query->read_user , [(int) $this->user->id]));
        $data = $query->save();
        return compact('data');
    }

    /**
     * @Access(admin=true)
     * @param string $title
     * @param string $image
     */
    public function setNotificationTestAction( string $title = 'Hello World' , string $image = 'system/notifications:images/update.svg')
    {
        $query = Notifications::create([
            'date' => new \DateTime(),
        ]);

        $query->title = $title;
        $query->image = $image;
        $query->save();
    }
}
