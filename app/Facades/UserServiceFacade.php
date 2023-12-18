<?php

namespace App\Facades;

use Psr\Container\ContainerInterface;

class UserServiceFacade extends Facade
{
    const FACADE_ACCESSOR = 'user_service';

    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __get($name)
    {
        return $this->container->get(self::FACADE_ACCESSOR);
    }
}
