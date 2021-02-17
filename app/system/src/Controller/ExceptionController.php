<?php

namespace GreenCheap\System\Controller;

use GreenCheap\Application as App;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ExceptionController
 * @package GreenCheap\System\Controller
 */
class ExceptionController
{
    /**
     * Converts an Exception to a Response.
     *
     * @param  FlattenException $exception
     * @return Response
     */
    public function showAction(FlattenException $exception): Response
    {
        $request = new Request();
        if($exception->getCode() === 0){
            $exception->setCode(500);
        }
        if (is_subclass_of($exception->getClass(), 'GreenCheap\Kernel\Exception\HttpException')) {
            $message = $exception->getMessage();
            $title = $exception->getCode() === 404 ? __('Page Not Found'):__('An error has been encountered');
        }else{
            $title = __('An error has been encountered');
            $message = __('Whoops, looks like something went wrong.');
        }

        $content  = $this->getAndCleanOutputBuffering($request->headers->get('X-Php-Ob-Level', -1));
        $response = App::view('system/error.php', compact('title', 'exception','message', 'content'));

        return App::response($response, $exception->getCode(), $exception->getHeaders());
    }

    /**
     * Cleans output buffer.
     *
     * @param  int    $level
     * @return string
     */
    protected function getAndCleanOutputBuffering($level): string
    {
        if (ob_get_level() <= $level) {
            return '';
        }

        Response::closeOutputBuffers($level + 1, true);

        return ob_get_clean();
    }
}
