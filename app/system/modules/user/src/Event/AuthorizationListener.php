<?php

namespace GreenCheap\User\Event;

use GreenCheap\Application as App;
use GreenCheap\Auth\Auth;
use GreenCheap\Auth\Event\AuthenticateEvent;
use GreenCheap\Auth\Event\AuthorizeEvent;
use GreenCheap\Auth\Exception\AuthException;
use GreenCheap\Event\EventSubscriberInterface;
use GreenCheap\User\Auth\UserProvider;

class AuthorizationListener implements EventSubscriberInterface
{
    /**
     * Initialize system.
     */
    public function onSystemInit()
    {
        App::auth()->setUserProvider(new UserProvider(App::get('auth.password')));
    }

    /**
     * Logout blocked users.
     */
    public function onRequest()
    {
        if ($user = App::auth()->getUser() and $user->isBlocked()) {
            App::auth()->logout();
        }
    }

    /**
     * Blocks users that are either not activated or blocked.
     *
     * @param  AuthorizeEvent $event
     * @throws AuthException
     */
    public function onAuthorize(AuthorizeEvent $event)
    {
        if ($event->getUser()->isBlocked()) {
            throw new AuthException($event->getUser()->login ? __('Your account is blocked.') : __('Your account has not been activated.'));
        }
    }

    /**
     * Redirects a user after successful login.
     */
    public function onLogin()
    {
        App::session()->migrate();
    }

    public function onSuccess()
    {
        App::session()->remove(Auth::LAST_USERNAME);
    }

    public function onFailure(AuthenticateEvent $event)
    {
        $credentials = $event->getCredentials();
        App::session()->set(Auth::LAST_USERNAME, $credentials['username']);
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'request' => [
                ['onRequest', 0],
                ['onSystemInit', 50]
            ],
            'auth.authorize' => 'onAuthorize',
            'auth.login'     => ['onLogin', -8],
            'auth.success'    => 'onSuccess',
            'auth.failure'    => 'onFailure'
        ];
    }
}
