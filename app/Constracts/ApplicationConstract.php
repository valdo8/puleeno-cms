<?php

namespace App\Constracts;

use Psr\Http\Server\RequestHandlerInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;

interface ApplicationConstract extends RouteCollectorProxyInterface, RequestHandlerInterface
{
    public function booted();

    public function isBooted();

    public function terminate();
}
