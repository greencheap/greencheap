<?php

namespace GreenCheap\Kernel\Exception;

use JetBrains\PhpStorm\Pure;

class MethodNotAllowedException extends HttpException
{
    /**
     * {@inheritdoc}
     */
    #[Pure] public function __construct($message = null, $previous = null, $code = 405)
    {
        parent::__construct($message ?: 'Method Not Allowed', $previous, $code);
    }
}
