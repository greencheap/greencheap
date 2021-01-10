<?php
namespace GreenCheap\Comment\Events;

use GreenCheap\Application as App;
use GreenCheap\Comment\Model\Comment;
use GreenCheap\User\Model\User;
use GreenCheap\Event\Event;
use GreenCheap\Event\EventSubscriberInterface;

/**
 * Class CommentListener
 * @package GreenCheap\Comment\Events
 */
class MentionListener implements EventSubscriberInterface
{
    protected array $mentions = [];

    /**
     * CommentListener constructor.
     * @param $config
     */
    public function __construct(public $config)
    {}

    /**
     * @param Event $event
     * @param Comment $comment
     */
    public function onNoticeManager(Event $event , Comment $comment)
    {
        $config = App::config('system/comment');

        if(!$config->get('attribute_people') || !$config->get('notify_reply')){
            return;
        }
        if($comment->get('notify_send') || $comment->status != Comment::STATUS_APPROVED){
            return;
        }

        $content = $comment->content;

        $content = preg_replace_callback('/@\w+/' , function($matches){
            foreach($matches as $match){
                $this->setMention($match);
            }
        }, $content);

        foreach($this->getMentions() as $username){
            $user = User::findByUsername(str_replace('@' , '', $username));

            $name = $user->name;
            $message = __('A user mentioned you. That\'s why we wanted to let you know.');
            $commentInformation = $comment->content;
            $link = $comment->getUrl(false);

            $mail = App::mailer()->create();
            $mail->setTo($user->email)
            ->setSubject(__('%site% - You were mentioned in a comment..', ['%site%' => App::module('system/site')->config('title')]))
            ->setBody(App::view('system/comment:mails/mention.php', compact(['message' , 'name' , 'commentInformation' , 'link'])), 'text/html')
            ->send();
        }

        $comment->set('notify_send' , true);
        $comment->save();
    }

    protected function setMention($name)
    {
        $this->mentions[] = $name;
    }

    /**
     * @return array
     */
    protected function getMentions(): array
    {
        return array_unique($this->mentions);
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe(): array
    {
        return [
            'model.comment.updated' => ['onNoticeManager' , 0],
        ];
    }
}
?>
