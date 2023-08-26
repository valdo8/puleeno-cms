<?php

namespace App\Core\Routing;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Routing\RouteCollectorProxy as SlimRouteCollectorProxy;

class RouteCollectorProxy extends SlimRouteCollectorProxy
{
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        CallableResolverInterface $callableResolver,
        ?ContainerInterface $container = null,
        ?RouteCollectorInterface $routeCollector = null,
        string $groupPattern = ''
    ) {
        $this->responseFactory = $responseFactory;
        $this->callableResolver = $callableResolver;
        $this->container = $container;
        $this->routeCollector = $routeCollector ?? new RouteCollector($responseFactory, $callableResolver, $container);
        $this->groupPattern = $groupPattern;
    }
}
