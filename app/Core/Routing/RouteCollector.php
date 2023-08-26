<?php

namespace App\Core\Routing;

use App\Core\Handlers\Strategies\RequestResponse;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\InvocationStrategyInterface;
use Slim\Interfaces\RouteInterface;
use Slim\Interfaces\RouteParserInterface;
use Slim\Routing\RouteCollector as SlimRouteCollector;
use Slim\Routing\RouteParser;

class RouteCollector extends SlimRouteCollector
{
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        CallableResolverInterface $callableResolver,
        ?ContainerInterface $container = null,
        ?InvocationStrategyInterface $defaultInvocationStrategy = null,
        ?RouteParserInterface $routeParser = null,
        ?string $cacheFile = null
    ) {
        $this->responseFactory = $responseFactory;
        $this->callableResolver = $callableResolver;
        $this->container = $container;
        $this->defaultInvocationStrategy = $defaultInvocationStrategy ?? new RequestResponse();
        $this->routeParser = $routeParser ?? new RouteParser($this);

        if ($cacheFile) {
            $this->setCacheFile($cacheFile);
        }
    }

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
