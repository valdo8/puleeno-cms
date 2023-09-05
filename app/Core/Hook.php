<?php

namespace App\Core;

use App\Constracts\HookConstract;
use App\Exceptions\NotCallableException;

abstract class Hook implements HookConstract
{
    protected $callable = null;

    protected $priority = 10;

    protected $paramsQuantity = 1;

    public function setPriority(int $priority)
    {
        $this->priority = $priority;
    }

    public function setCallable($callable)
    {
        if (!is_callable($callable)) {
            throw new NotCallableException(var_export($callable, true));
        }
        $this->callable = $callable;
    }

    public function setParamsQuantity(int $quantity)
    {
        $this->paramsQuantity = $quantity;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function getCallable()
    {
        return $this->callable;
    }

    public function getParamsQuantity(): int
    {
        return $this->paramsQuantity;
    }
}
