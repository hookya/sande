<?php

namespace Sande\Exception;

use Exception;
use Throwable;

class DecryptException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}