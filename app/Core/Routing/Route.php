<?php

namespace App\Core\Routing;

use App\Core\Handlers\Strategies\RequestResponse;
use App\Core\MiddlewareDispatcher;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Handlers\Strategies\RequestHandler;
use Slim\Interfaces\AdvancedCallableResolverInterface;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\InvocationStrategyInterface;
use Slim\Routing\Route as SlimRoute;

class Route extends SlimRoute
{
    /**
     * @param string[]                         $methods    The route HTTP methods
     * @param string                           $pattern    The route pattern
     * @param callable|string                  $callable   The route callable
     * @param ResponseFactoryInterface         $responseFactory
     * @param CallableResolverInterface        $callableResolver
     * @param ContainerInterface|null          $container
     * @param InvocationStrategyInterface|null $invocationStrategy
     * @param RouteGroupInterface[]            $groups     The parent route groups
     * @param int                              $identifier The route identifier
     */
    public function __construct(
        array $methods,
        string $pattern,
        $callable,
        ResponseFactoryInterface $responseFactory,
        CallableResolverInterface $callableResolver,
        ?ContainerInterface $container = null,
        ?InvocationStrategyInterface $invocationStrategy = null,
        array $groups = [],
        int $identifier = 0
    ) {
        $this->methods = $methods;
        $this->pattern = $pattern;
        $this->callable = $callable;
        $this->responseFactory = $responseFactory;
        $this->callableResolver = $callableResolver;
        $this->container = $container;
        $this->invocationStrategy = $invocationStrategy ?? new RequestResponse();
        $this->groups = $groups;
        $this->identifier = 'route' . $identifier;
        $this->middlewareDispatcher = new MiddlewareDispatcher($this, $callableResolver, $container);
    }

    /**
     * {@inheritdoc}
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->callableResolver instanceof AdvancedCallableResolverInterface) {
            $callable = $this->callableResolver->resolveRoute($this->callable);
        } else {
            $callable = $this->callableResolver->resolve($this->callable);
        }
        /**
         * @var \App\Core\Handlers\Strategies\RequestResponse
         */
        $strategy = $this->invocationStrategy;

        /** @var string[] $strategyImplements */
        $strategyImplements = class_implements($strategy);

        if (
            is_array($callable)
            && $callable[0] instanceof RequestHandlerInterface
            && !in_array(RequestHandlerInvocationStrategyInterface::class, $strategyImplements)
        ) {
            /**
             * @var \Slim\Handlers\Strategies\RequestHandler;
             */
            $strategy = new RequestHandler();
        }

        $response = $this->responseFactory->createResponse();

        /**
         * @var \DI\Container
         */
        $container = $this->container;

        $container->set(RequestInterface::class, $request);
        $container->set(ServerRequestInterface::class, $request);
        $container->set('request', $request);

        $container->set(ResponseInterface::class, $response);
        $container->set('response', $response);

        $container->set('args', $this->arguments);

        // Auto resolve params
        if (is_a($strategy, RequestResponse::class)) {
            return $strategy->resolve($callable, $request, $response, $this->arguments, $container);
        }
        return $strategy($callable, $request, $response, $this->arguments);
    }
}
