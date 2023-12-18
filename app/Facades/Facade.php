<?php

namespace App\Facades;

use Psr\Container\ContainerInterface;

abstract class Facade
{
    protected static $container;

    public function __construct(ContainerInterface $container)
    {
        if (is_null(static::$container)) {
            static::setContainer($container);
        }
    }

    public static function setContainer(ContainerInterface $container)
    {
        static::$container = $container;
    }

    public static function getFacadeAccessor()
    {
        throw new \RuntimeException('Facade does not have a facade accessor.');
    }

    public static function __callStatic($method, $args)
    {
        $instance = static::$container->get(static::getFacadeAccessor());
        if (! method_exists($instance, $method)) {
            throw new \BadMethodCallException(sprintf('Method %s does not exist on facade %s.', $method, get_class($instance)));
        }

        return $instance->$method(...$args);
    }
}
