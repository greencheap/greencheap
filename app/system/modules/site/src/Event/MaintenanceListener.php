<?php

namespace GreenCheap\Site\Event;

use GreenCheap\Application as App;
use GreenCheap\Event\EventSubscriberInterface;

class MaintenanceListener implements EventSubscriberInterface
{
    /**
     * Puts the page in maintenance mode.
     */
    public function onRequest($event, $request)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $site = App::module('system/site');

        if ($site->config('maintenance.enabled') && !(App::isAdmin() || $request->attributes->get('_maintenance') || App::user()->hasAccess('site: maintenance access') || App::user()->hasAccess('system: access admin area'))) {

            $message = $site->config('maintenance.msg') ?: __("We make updates at the back. That's why you can't access our website right now. If you have any questions, you can contact us through social networks.");
            $logo = $site->config('maintenance.logo') ?: 'app/system/modules/theme/assets/images/greencheap-logo.svg';
            $response = App::view('system/theme:views/maintenance.php', compact('message', 'logo'));

            $request->attributes->set('_disable_debugbar', true);

            $types = $request->getAcceptableContentTypes();

            if (!App::user()->isAuthenticated() && $request->isXMLHttpRequest()) {
                App::abort('401', 'Unauthorized');
            } elseif ('json' == $request->getFormat(array_shift($types))) {
                $response = App::response()->json($message, 503);
            } else {
                $response = App::response($response, 503);
            }

            $event->setResponse($response);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'request' => ['onRequest', 10]
        ];
    }
}
