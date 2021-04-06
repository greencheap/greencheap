<?php

namespace GreenCheap\Content\Plugin;

use GreenCheap\Application as App;
use GreenCheap\Content\Event\ContentEvent;
use GreenCheap\Event\EventSubscriberInterface;

class MarkdownPlugin implements EventSubscriberInterface
{
    /**
     * Content plugins callback.
     *
     * @param ContentEvent $event
     */
    public function onContentPlugins(ContentEvent $event)
    {
        if (!$event["markdown"]) {
            return;
        }

        $content = $event->getContent();
        $content = App::markdown()->parse($content, is_array($event["markdown"]) ? $event["markdown"] : []);

        $event->setContent($content);
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            "content.plugins" => ["onContentPlugins", 5],
        ];
    }
}
