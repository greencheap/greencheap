<?php
namespace GreenCheap\Categories\Model;

use GreenCheap\Database\ORM\ModelTrait;
use GreenCheap\System\Model\DataModelTrait;
use GreenCheap\System\Model\StatusModelTrait;

/**
 * @Entity(tableClass="@system_categories")
 */
class Categories implements \JsonSerializable
{
    use DataModelTrait, ModelTrait, StatusModelTrait;

    /**
     * @Column(type="integer")
     * @Id
     */
    public $id;

    /**
     * @Column(type="string")
     */
    public $title;

    /**
     * @Column(type="string")
     */
    public $slug;

    /**
     * @Column(type="integer")
     */
    public $user_id;

    /**
     * @Column(type="integer")
     */
    public $sub_category;

    /**
     * @Column(type="string")
     */
    public $type;

    /**
     * @Column(type="datetime")
     */
    public $date;

    /**
     * @Column(type="datetime")
     */
    public $modified;

    /**
     * @Column(type="integer")
     */
    public $status;

    /**
     * @BelongsTo(targetEntity="GreenCheap\User\Model\User" , keyFrom="user_id")
     */
    public $user;

    /**
     * @var array
     */
    protected static $properties = [
        'author' => 'getAuthor',
        'published' => 'isPublished',
        'accessible' => 'isAccessible',
        'has_subcategory' => 'hasSubCategory'
    ];

    /**
     * @return array|bool
     */
    public function getAuthor()
    {
        if($this->user){
            return [
                'username' => $this->user->username,
                'email'    => $this->user->email
            ];
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isPublished()
    {
        return $this->status === self::getStatus('STATUS_PUBLISHED') && $this->date < new \DateTime;
    }

    /**
     * @return bool
     */
    public function isAccessible()
    {
        return $this->isPublished();
    }

    /**
     * @return bool
     */
    public function hasSubCategory()
    {
        $query = $this->where('sub_category = ?' , [$this->id])->get();
        if($query){
            return true;
        }
        return false;
    }

    /**
     * Event
     * @param $event
     * @param Categories $categories
     */
    public function saving( $event , Categories $categories ){
        $i  = 2;
        $a = 2;
        $id = $categories->id;

        $categories->modified = new \DateTime();

        while (self::where('title = ?', [$categories->title])->where(function ($query) use ($id) {
            if ($id) {
                $query->where('id <> ?', [$id]);
            }
        })->first()) {
            $categories->title = preg_replace('/-\d+$/', '', $categories->title).'-'.$a++;
        }

        while (self::where('slug = ?', [$categories->slug])->where(function ($query) use ($id) {
            if ($id) {
                $query->where('id <> ?', [$id]);
            }
        })->first()) {
            $categories->slug = preg_replace('/-\d+$/', '', $categories->slug).'-'.$i++;
        }
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        $data = [];
        return $this->toArray($data);
    }

}
?>
