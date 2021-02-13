<?php

namespace GreenCheap\User\Controller;

use GreenCheap\Application as App;
use GreenCheap\Auth\Auth;
use GreenCheap\Auth\Exception\AuthException;
use GreenCheap\Auth\Exception\BadCredentialsException;
use GreenCheap\Routing\Annotation\Request;
use GreenCheap\Routing\Annotation\Route;
use GreenCheap\Session\Csrf\Exception\CsrfException;

/**
 * Class AuthController
 * @package GreenCheap\User\Controller
 */
class AuthController
{
    /**
     * @Route(defaults={"_maintenance"=true})
     * @Request({"redirect"})
     * @param string $redirect
     * @return array
     */
    public function loginAction($redirect = ''): array
    {
        if (!$redirect) {
            $redirect = App::url(App::config('system/user')['login_redirect']);
        }

		if (App::user()->isAuthenticated()) {
            return $this->redirect($redirect);
		}

        return [
            '$view' => [
                'title' => __('Login'),
                'name' => 'system/user/login.php'
            ],
            'last_username' => App::session()->get(Auth::LAST_USERNAME),
            'redirect' => $redirect
        ];
    }

    /**
     * @Route(defaults={"_maintenance" = true})
     * @Request({"redirect": "string"})
     * @param string $redirect
     * @return mixed
     */
    public function logoutAction($redirect = ''): mixed
    {
        if (($event = App::auth()->logout()) && $event->hasResponse()) {
            return $event->getResponse();
        }

        return $this->redirect($redirect);
    }

    /**
     * @Route(methods="POST", defaults={"_maintenance" = true})
     * @Request({"credentials": "array", "remember_me": "boolean", "redirect": "string"})
     * @param $credentials
     * @param bool $remember
     * @param string $redirect
     * @return mixed
     */
    public function authenticateAction($credentials, $remember = false, $redirect = ''): mixed
    {
        try {

            if (!App::csrf()->validate()) {
                throw new CsrfException(__('Invalid token. Please try again.'));
            }

            App::auth()->authorize($user = App::auth()->authenticate($credentials, false));

            if (($event = App::auth()->login($user, $remember)) && $event->hasResponse()) {
                return $event->getResponse();
            }

            if (App::request()->isXmlHttpRequest()) {
                return App::response()->json(['csrf' => App::csrf()->generate()]);
            } else {
                return $this->redirect($redirect);
            }

        } catch (CsrfException $e) {
            if (App::request()->isXmlHttpRequest()) {
                return App::response()->json(['csrf' => App::csrf()->generate()], 401);
            }
            $error = $e->getMessage();
        } catch (BadCredentialsException $e) {
            $error = __('Invalid username or password.');
        } catch (AuthException $e) {
            $error = $e->getMessage();
        }

        if (App::request()->isXmlHttpRequest()) {
            return App::response()->json($error, 401);
        } else {
            App::message()->error($error);
            return $this->redirect(App::url()->previous());
        }
    }

    /**
     * @param $url
     * @return mixed
     */
    protected function redirect($url): mixed
    {
        do {
            $url = preg_replace('#^(https?:)?//[^/]+#', '', $url, 1, $count);
        } while ($count);
        return App::redirect($url);
    }
}
