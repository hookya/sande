<?php

namespace Sande\Exception;

use InvalidArgumentException as BaseInvalidArgumentException;
use Throwable;

class InvalidArgumentException extends BaseInvalidArgumentException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}