<?php

namespace GreenCheap\Comment\Model;

use GreenCheap\Database\ORM\ModelTrait;
use GreenCheap\System\Model\DataModelTrait;

trait CommentModelTrait
{
    use ModelTrait , DataModelTrait;

    /**
     * @Deleting
     */
    public static function deleting($event, Comment $comment)
    {
        //self::where(['parent_id = :old_parent'], [':old_parent' => $comment->id])->update(['parent_id' => $comment->parent_id]);
    }
}
