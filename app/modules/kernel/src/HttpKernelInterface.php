<?php

namespace GreenCheap\Kernel;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface HttpKernelInterface
{
    /**
     * Gets the current request.
     *
     * @return mixed
     */
    public function getRequest(): mixed;

    /**
     * Checks if this is a master request.
     *
     * @return bool
     */
    public function isMasterRequest(): bool;

    /**
     * Handles the request.
     *
     * @param  Request $request
     * @return Response
     */
    public function handle(Request $request): Response;

    /**
     * Aborts the current request with HTTP exception.
     *
     * @param int $code
     * @param null $message
     * @param array $headers
     * @throws HttpException
     */
    public function abort(int $code, $message = null, array $headers = []);

    /**
     * Terminates the current request.
     *
     * @param Request  $request
     * @param Response $response
     */
    public function terminate(Request $request, Response $response);
}
