<?php

namespace GreenCheap\Session\Csrf\Event;

use GreenCheap\Event\EventSubscriberInterface;
use GreenCheap\Session\Csrf\Exception\CsrfException;
use GreenCheap\Session\Csrf\Provider\CsrfProviderInterface;

class CsrfListener implements EventSubscriberInterface
{
    /**
     * @var CsrfProviderInterface
     */
    protected $provider;

    /**
     * Constructor.
     *
     * @param CsrfProviderInterface $provider
     */
    public function __construct(CsrfProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Checks for the CSRF token and throws 401 exception if invalid.
     *
     * @param  $event
     * @throws UnauthorizedException
     */
    public function onRequest($event, $request)
    {
        $this->provider->setToken($request->get("_csrf", $request->headers->get("X-XSRF-TOKEN")));
        $attributes = $request->attributes->get("_request", []);

        if (isset($attributes["csrf"]) && !$this->provider->validate()) {
            throw new CsrfException("Invalid CSRF token.");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            "request" => ["onRequest", -150],
        ];
    }
}
