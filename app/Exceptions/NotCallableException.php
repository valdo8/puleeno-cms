<?php

namespace App\Exceptions;

use RuntimeException;

class NotCallableException extends RuntimeException
{
    public function __construct($callable = '', $code = 0)
    {
        if ($callable !== '') {
            $message = sprintf('%s is not callable', $callable);
        } else {
            $message = $callable;
        }
        parent::__construct($message, $code);
    }
}
