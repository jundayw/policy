<?php

namespace Jundayw\Policy\Exceptions;

use RuntimeException;
use Throwable;

class PolicyException extends RuntimeException
{
    public function __construct(string $message = 'Forbidden', int $code = 403, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
