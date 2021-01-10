<?php

namespace GreenCheap\Kernel\Exception;

use JetBrains\PhpStorm\Pure;

class InternalErrorException extends HttpException
{
    /**
     * {@inheritdoc}
     */
    #[Pure] public function __construct($message = null, $previous = null, $code = 500)
    {
        parent::__construct($message ?: 'Internal Server Error', $previous, $code);
    }
}
