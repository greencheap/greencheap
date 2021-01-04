<?php

namespace GreenCheap\Comment\Model;

/**
 * Class Comment
 * @package GreenCheap\Comment\Model
 * @Entity(tableClass="@system_comments")
 */
class Comment implements \JsonSerializable
{
    use CommentModelTrait;

    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_SPAM = 2;

    /**
     * @Column(type="integer")
     * @Id
     */
    public $id;

    /**
     * @Column(type="integer")
     */
    public $parent_id = 0;

    /**
     * The id to match for the working plugin: Example Blog/Model/Post -> id
     * @Column(type="integer")
     */
    public $own_id;

    /**
     * @Column(type="string")
     */
    public $type;

    /**
     * @Column(type="integer")
     */
    public $user_id;

    /**
     * @Column(type="datetime")
     */
    public $created;

    /**
     * @Column(type="text")
     */
    public $content = '';

    /**
     * @Column(type="smallint")
     */
    public $status;

    /**
     * @BelongsTo(targetEntity="GreenCheap\User\Model\User", keyFrom="user_id")
     */
    public $user;

    /**
     * @return array
     */
    protected static $properties = [
        'author' => 'getAuthor',
        'hasChildComment' => 'hasChildComment'
    ];

    public function getAuthor():array|bool
    {
        if($this->user){
            return [
                'name' => $this->user->name,
                'email' => $this->user->email
            ];
        }
        return false;
    }

    public function hasChildComment()
    {
        $query = self::where([
            'status = :status',
            'parent_id = :parent_id',
            'type = :type',
            'own_id = :own_id'
        ], [
            'status' => self::STATUS_APPROVED,
            'parent_id' => $this->id,
            'type' => $this->type,
            'own_id' => $this->own_id
        ])->related('user')->get();
        if($query){
            return $query;
        }
        return false;
    }

    public static function getStatuses():array
    {
        return [
            self::STATUS_PENDING => __('Pending'),
            self::STATUS_APPROVED => __('Approved'),
            self::STATUS_SPAM => __('Spam'),
        ];
    }

    /**
     * It is the algorithm that prevents frequent comments.
     * @param comment
     * @param date 
     */
    public static function isInterpretationThreshold(array $comment = [], string $date = null):bool
    {
        if($date){
            $datetime = new \DateTime();
            $query = self::where(
                [
                    'user_id = :user_id',
                    'own_id = :own_id',
                    'type = :type',
                    'created >= :date'
                ], [
                    'user_id' => $comment['user_id'],
                    'own_id' => $comment['own_id'],
                    'type' => $comment['type'],
                    'date' => $datetime->modify($date)
                ]
            )->first();
            if($query){
                return true;
            }
        }
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
