<?php

namespace App\Exceptions;

use Exception;

class ClassNotFoundException extends Exception
{
    public function __construct($className, $code = null)
    {
        parent::__construct(sprintf('Class "%s" not found', $className), $code);
    }
}
