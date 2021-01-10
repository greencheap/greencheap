<?php

namespace GreenCheap\Kernel\Exception;

use JetBrains\PhpStorm\Pure;

class UnauthorizedException extends HttpException
{
    /**
     * {@inheritdoc}
     */
    #[Pure] public function __construct($message = null, $previous = null, $code = 401)
    {
        parent::__construct($message ?: 'Unauthorized', $previous, $code);
    }
}
