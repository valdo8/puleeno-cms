<?php

namespace App\Core\Routing;

use Slim\Interfaces\RouteInterface;
use Slim\Routing\RouteCollector as SlimRouteCollector;

class RouteCollector extends SlimRouteCollector
{
    /**
     * @param string[]        $methods
     * @param callable|string $callable
     */
    protected function createRoute(array $methods, string $pattern, $callable): RouteInterface
    {
        return new Route(
            $methods,
            $pattern,
            $callable,
            $this->responseFactory,
            $this->callableResolver,
            $this->container,
            $this->defaultInvocationStrategy,
            $this->routeGroups,
            $this->routeCounter
        );
    }
}
