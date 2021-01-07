<?php
namespace GreenCheap\Comment\Events;

use GreenCheap\Application as App;
use GreenCheap\Comment\Model\Comment;
use GreenCheap\Event\Event;
use GreenCheap\Event\EventSubscriberInterface;

/**
 * Class CommentListener
 * @package GreenCheap\Comment\Events
 */
class CommentListener implements EventSubscriberInterface
{

    /**
     * CommentListener constructor.
     * @param $config
     */
    public function __construct(public $config)
    {}

    /**
     * It has been developed to inform the manager that a new response has been received.
     * @param Event $event
     * @param $comment
     */
    public function onNoticeManager(Event $event , Comment $comment)
    {
        $status = $comment->status;

        if(Comment::STATUS_PENDING != $status){
            return;
        }
        $message = __('A new comment has been added on your website. This comment is waiting for your approval.');

        $mail = App::mailer()->create();
        $mail->setTo(App::module('system/mail')->config('from_address'))
        ->setSubject(__('%site% - Member commented.', ['%site%' => App::module('system/site')->config('title')]))
        ->setBody(App::view('system/comment:mails/pending.php', compact('message')), 'text/html')
        ->send();
    }

    /**
     * @param Event $event
     * @param $comment
     */
    public function onDeleted(Event $event , Comment $comment)
    {
        $deleted = Comment::where('parent_id = :old_id' , ['old_id' => $comment->id])->get();
        foreach($deleted as $dlt){
            $dlt->delete();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe(): array
    {
        return [
            'model.comment.created' => ['onNoticeManager' , 0],
            'model.comment.deleting' => ['onDeleted' , 0],
        ];
    }
}
?>
