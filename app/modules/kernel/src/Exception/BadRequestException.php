<?php

namespace GreenCheap\Kernel\Exception;

use JetBrains\PhpStorm\Pure;

class BadRequestException extends HttpException
{
    /**
     * {@inheritdoc}
     */
    #[Pure] public function __construct($message = null, $previous = null, $code = 400)
    {
        parent::__construct($message ?: 'Bad Request', $previous, $code);
    }
}
