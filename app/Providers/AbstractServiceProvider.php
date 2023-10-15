<?php

namespace App\Providers;

use Psr\Container\ContainerInterface;
use App\Services\UserService;

abstract class AbstractServiceProvider
{
    protected ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    abstract public function register();

    abstract public function boot();
}
