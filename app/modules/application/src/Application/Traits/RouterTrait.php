<?php

namespace GreenCheap\Application\Traits;

use GreenCheap\Kernel\Event\ExceptionListenerWrapper;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

trait RouterTrait
{
    /**
     * @param $code
     * @param null $message
     * @param array $headers
     * @see HttpKernel::abort()
     */
    public static function abort($code, $message = null, array $headers = [])
    {
        static::kernel()->abort($code, $message, $headers);
    }

    /**
     * @param int $code
     * @param null $message
     * @return JsonResponse
     */
    public static function jsonabort(int $code = 500, $message = null): JsonResponse
    {
        return new JsonResponse($message, $code);
    }

    /**
     * Registers an error handler.
     *
     * @param mixed $callback
     * @param integer $priority
     */
    public static function error(mixed $callback, $priority = -8)
    {
        static::events()->on('exception', new ExceptionListenerWrapper($callback), $priority);
    }

    /**
     * @param string $url
     * @param array $parameters
     * @param int $status
     * @param array $headers
     * @return mixed
     * @see Router::redirect()
     */
    public static function redirect($url = '', $parameters = [], $status = 302, $headers = []): mixed
    {
        return static::router()->redirect($url, $parameters, $status, $headers);
    }

    /**
     * Handles a subrequest to forward an action internally.
     *
     * @param  string $name
     * @param  array  $parameters
     * @throws \RuntimeException
     * @return Response
     */
    public static function forward($name, $parameters = [])
    {
        if (!$request = static::request()) {
            throw new \RuntimeException('No Request set.');
        }

        return static::kernel()->handle(
            Request::create(
                static::router()->generate($name, $parameters), 'GET', [],
                $request->cookies->all(), [],
                $request->server->all()
            ));
    }
}
