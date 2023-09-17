<?php

namespace App\Exceptions;

use Exception;

class NotFoundExtensionException extends Exception
{
    public function __construct($extension, $code = 0)
    {
        parent::__construct(sprintf('Extension "%s" not found', $extension), $code);
    }
}
