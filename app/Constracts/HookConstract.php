<?php

namespace App\Constracts;

interface HookConstract
{
    public function setPriority(int $priority);

    public function setCallable($callable);

    public function setParamsQuantity(int $quantity);

    public function getPriority(): int;

    public function getCallable();

    public function getParamsQuantity(): int;

    public static function create($fn, $priority = 10, $paramQuantity = 1): HookConstract;
}
