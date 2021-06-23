<?php

namespace GreenCheap\View\Event;

use GreenCheap\Application as App;
use GreenCheap\Event\EventSubscriberInterface;
use JetBrains\PhpStorm\ArrayShape;

class ResponseListener implements EventSubscriberInterface
{
    const REGEX_URL = '/
                        \s                              # match a space
                        (?<attr>href|src|poster)=       # match the attribute
                        ([\"\'])                        # start with a single or double quote
                        (?!\/|\#|[a-z0-9\-\.]+\:)       # make sure it is a relative path
                        (?<url>[^\"\'>]+)               # match the actual src value
                        \2                              # match the previous quote
                       /xiU';

    /**
     * Filter the response content.
     */
    public function onResponse($event, $request, $response)
    {
        if (!is_string($content = $response->getContent())) {
            return;
        }

        $response->setContent(
            preg_replace_callback(
                self::REGEX_URL,
                function ($matches) {
                    return sprintf(' %s="%s"', $matches["attr"], App::url($matches["url"]));
                },
                $content
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    #[ArrayShape(["response" => "array"])]
    public function subscribe(): array
    {
        return [
            "response" => ["onResponse", -20],
        ];
    }
}
