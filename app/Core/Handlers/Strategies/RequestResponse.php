<?php

namespace App\Core\Handlers\Strategies;

use App\Exceptions\CanNotResolveParamException;
use Closure;
use Laravel\SerializableClosure\Support\ReflectionClosure;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionNamedType;
use Slim\Handlers\Strategies\RequestResponse as SlimRequestResponse;

class RequestResponse extends SlimRequestResponse
{
    /**
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;

    /**
     * Invoke a route callable with request, response, and all route parameters
     * as an array of arguments.
     *
     * @param array<string, string>  $routeArguments
     */
    public function resolve(
        callable $callable,
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $routeArguments,
        ContainerInterface $container
    ): ResponseInterface {
        $this->container = $container;

        foreach ($routeArguments as $k => $v) {
            $request = $request->withAttribute($k, $v);
        }

        // Resolved params order
        $params = $this->resolveParams($callable, $request, $response);

        // Send $routeArguments to last param
        array_push(
            $params,
            $routeArguments
        );

        return call_user_func_array($callable, $params);
    }

    protected function resolveTheParamValue(?ReflectionNamedType $type, string $namedParam)
    {
        if (!is_null($type) && $this->container->has($type->getName())) {
            return $this->container->get($type->getName());
        }
        if ($this->container->has($namedParam)) {
            return $this->container->get($namedParam);
        }
        throw new CanNotResolveParamException();
    }


    protected function resolveParamsForClosure($callable)
    {
        $params = [];

        $methodRefl = new ReflectionClosure($callable);
        if ($methodRefl->getNumberOfParameters() > 0) {
            foreach ($methodRefl->getParameters() as $param) {
                $params[] = $this->resolveTheParamValue(
                    $param->getType(),
                    $param->getName()
                );
            }
        }

        return $params;
    }

    protected function resolveParamsForStaticMethod($callable): array
    {
        $params = [];

        $funcRefl = new ReflectionFunction($callable);

        if ($funcRefl->getNumberOfParameters() > 0) {
            foreach ($funcRefl->getParameters() as $param) {
                $params[] = $this->resolveTheParamValue(
                    $param->getType(),
                    $param->getName()
                );
            }
        }

        return $params;
    }

    protected function resolveParamsForMethod($callable): array
    {
        $params = [];
        if (count($callable) === 2) {
            $methodRefl = new ReflectionMethod($callable[0], $callable[1]);
            if ($methodRefl->getNumberOfParameters() > 0) {
                foreach ($methodRefl->getParameters() as $param) {
                    $params[] = $this->resolveTheParamValue(
                        $param->getType(),
                        $param->getName()
                    );
                }
            }
        }

        return $params;
    }

    protected function resolveParams($callable, $request, $response): array
    {
        try {
            if ($callable instanceof Closure) {
                return $this->resolveParamsForClosure($callable);
            } elseif (is_string($callable)) {
                return $this->resolveParamsForStaticMethod($callable);
            } elseif (is_array($callable) && is_object($callable[0])) {
                $reflectCls = new ReflectionClass($callable[0]);
                if (!$reflectCls->isAnonymous()) {
                    return $this->resolveParamsForMethod($callable);
                }
            }
        } catch (CanNotResolveParamException $e) {
            // Send PHP error logs to debug
            error_log($e->getMessage());
        }

        return [$request, $response];
    }
}
