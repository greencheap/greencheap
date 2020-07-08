<?php
namespace GreenCheap\Notifications\Model;

use GreenCheap\Application as App;
use GreenCheap\Database\ORM\Annotation\Entity;
use GreenCheap\Database\ORM\ModelTrait;
use GreenCheap\System\Model\DataModelTrait;

/**
 * Class Notifications
 * @package GreenCheap\Notifications\Model
 * @Entity(tableClass="@system_notifications")
 */
class Notifications implements \JsonSerializable
{
    use ModelTrait, DataModelTrait;

    /**
     * @Column(type="integer")
     * @Id
     */
    public $id;

    /**
     * @Column
     */
    public $title;

    /**
     * @Column
     */
    public $image;

    /**
     * @Column(type="simple_array")
     */
    public $except_group;

    /**
     * @Column(type="simple_array")
     */
    public $except_user;

    /**
     * @Column(type="simple_array")
     */
    public $read_group;

    /**
     * @Column(type="simple_array")
     */
    public $read_user;

    /**
     * @Column(type="datetime")
     */
    public $date;

    /**
     * @var string[]
     */
    protected static $properties = [
        'is_read' => 'isRead',
        'is_read_string' => 'isReadString',
        'image_url' => 'getImage'
    ];

    /**
     * @return bool
     */
    public function isRead()
    {
        $user = App::user();
        if(!$user->isAuthenticated()){
            return false;
        }
        if(in_array($user->id , $this->read_user)){
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public function isReadString(){
        if($this->isRead()){
            return 'read';
        }
        return 'unread';
    }

    /**
     * @return mixed
     */
    public function getImage(){
        return App::url()->getStatic($this->image);
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
?>
