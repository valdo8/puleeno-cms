<?php

namespace App\Core\Routing;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Handlers\Strategies\RequestHandler;
use Slim\Interfaces\AdvancedCallableResolverInterface;
use Slim\Routing\Route as SlimRoute;

class Route extends SlimRoute
{
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
        $strategy = $this->invocationStrategy;


        /** @var string[] $strategyImplements */
        $strategyImplements = class_implements($strategy);

        if (
            is_array($callable)
            && $callable[0] instanceof RequestHandlerInterface
            && !in_array(RequestHandlerInvocationStrategyInterface::class, $strategyImplements)
        ) {
            $strategy = new RequestHandler();
        }

        $response = $this->responseFactory->createResponse();
        return $strategy($callable, $request, $response, $this->arguments);
    }
}
