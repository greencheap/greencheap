<?php

namespace GreenCheap\Kernel\Exception;

class HttpException extends \RuntimeException
{
    /**
     * Constructor.
     *
     * @param string $message
     * @param null $previous
     * @param int $code
     */
    public function __construct(string $message, $previous = null, $code = 500)
    {
        parent::__construct($message, $code, $previous);
    }
}
