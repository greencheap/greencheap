<?php

namespace GreenCheap\Kernel\Exception;

use JetBrains\PhpStorm\Pure;

class ForbiddenException extends HttpException
{
    /**
     * {@inheritdoc}
     */
    #[Pure] public function __construct($message = null, $previous = null, $code = 403)
    {
        parent::__construct($message ?: 'Forbidden', $previous, $code);
    }
}
