<?php

namespace GreenCheap\Kernel;

use GreenCheap\Event\EventDispatcherInterface;
use GreenCheap\Kernel\Event\ControllerEvent;
use GreenCheap\Kernel\Event\ExceptionEvent;
use GreenCheap\Kernel\Event\KernelEvent;
use GreenCheap\Kernel\Event\RequestEvent;
use GreenCheap\Kernel\Exception\BadRequestException;
use GreenCheap\Kernel\Exception\ConflictException;
use GreenCheap\Kernel\Exception\ForbiddenException;
use GreenCheap\Kernel\Exception\HttpException;
use GreenCheap\Kernel\Exception\InternalErrorException;
use GreenCheap\Kernel\Exception\MethodNotAllowedException;
use GreenCheap\Kernel\Exception\NotFoundException;
use GreenCheap\Kernel\Exception\UnauthorizedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class HttpKernel implements HttpKernelInterface
{
    /**
     * @var EventDispatcherInterface
     */
    protected EventDispatcherInterface $events;

    /**
     * @var RequestStack
     */
    protected RequestStack $stack;

    /**
     * Constructor.
     *
     * @param EventDispatcherInterface $events
     * @param RequestStack|null $stack
     */
    public function __construct(EventDispatcherInterface $events, RequestStack $stack = null)
    {
        $this->events = $events;
        $this->stack  = $stack ?: new RequestStack();
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest(): Request
    {
        return $this->stack->getCurrentRequest();
    }

    /**
     * {@inheritdoc}
     */
    public function isMasterRequest(): bool
    {
        return $this->getRequest() === $this->stack->getMasterRequest();
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function handle(Request $request): Response
    {
        try {
            $event = new RequestEvent('request', $this);

            $this->stack->push($request);

            $this->events->trigger($event, [$request]);

            if ($event->hasResponse()) {
                $response = $event->getResponse();
            } else {
                $response = $this->handleController();
            }

            return $this->handleResponse($response);

        } catch (\Exception $e) {

            return $this->handleException($e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function abort(int $code, $message = null, array $headers = [])
    {
        throw match ($code) {
            400 => new BadRequestException($message),
            401 => new UnauthorizedException($message),
            403 => new ForbiddenException($message),
            404 => new NotFoundException($message),
            405 => new MethodNotAllowedException($message),
            409 => new ConflictException($message),
            500 => new InternalErrorException($message),
            default => new HttpException($message),
        };
    }

    /**
     * {@inheritdoc}
     */
    public function terminate(Request $request, Response $response)
    {
        $event = new KernelEvent('terminate', $this);
        $this->events->trigger($event, [$request, $response]);
    }

    /**
     * Handles the controller event.
     *
     * @return Response
     */
    protected function handleController(): Response
    {
        $event = new ControllerEvent('controller', $this);
        $this->events->trigger($event, [$this->getRequest()]);

        $response = $event->getResponse();

        if (!$response instanceof Response) {

            $msg = 'The controller must return a response.';

            if ($response === null) {
                $msg .= ' Did you forget to add a return statement somewhere in your controller?';
            }

            throw new \LogicException($msg);
        }

        return $response;
    }

    /**
     * Handles the response event.
     *
     * @param  Response $response
     * @return Response
     */
    protected function handleResponse(Response $response): Response
    {
        $event = new KernelEvent('response', $this);
        $this->events->trigger($event, [$this->getRequest(), $response]);
        $this->stack->pop();

        return $response;
    }

    /**
     * Handles the exception event.
     *
     * @param  \Exception $e
     * @return Response
     * @throws \Exception
     */
    protected function handleException(\Exception $e): Response
    {
        $event = new ExceptionEvent('exception', $this, $e);
        $this->events->trigger($event, [$this->getRequest()]);

        $e = $event->getException();

        if (!$event->hasResponse()) {
            throw $e;
        }

        $response = $event->getResponse();

        if (!$response->isClientError() && !$response->isServerError() && !$response->isRedirect()) {
            if ($e instanceof HttpException) {
                $response->setStatusCode($e->getCode());
            } else {
                $response->setStatusCode(500);
            }
        }

        try {
            return $this->handleResponse($response);
        } catch (\Exception $e) {
            return $response;
        }
    }
}
