<?php

namespace GreenCheap\User\Event;

use GreenCheap\Application as App;
use GreenCheap\Auth\Event\LoginEvent;
use GreenCheap\Event\EventSubscriberInterface;
use GreenCheap\User\Model\User;

class UserListener implements EventSubscriberInterface
{
    /**
     * Updates user's last login time
     */
    public function onUserLogin(LoginEvent $event)
    {
        User::updateLogin($event->getUser());
    }

    public function onRoleDelete($event, $role)
    {
        User::removeRole($role);
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'auth.login' => 'onUserLogin',
            'model.role.deleted' => 'onRoleDelete'
        ];
    }
}
