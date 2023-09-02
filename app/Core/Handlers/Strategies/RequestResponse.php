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
use ReflectionParameter;
use ReflectionType;
use Slim\Handlers\Strategies\RequestResponse as SlimRequestResponse;
use Throwable;

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
        $params = $this->resolveParams(
            $callable,
            $request,
            $response,
            $routeArguments
        );

        return call_user_func_array($callable, $params);
    }

    protected function resolveTheParamValue(?ReflectionType $type, string $namedParam)
    {
        if (!is_null($type) && $type instanceof ReflectionNamedType && $this->container->has($type->getName())) {
            return $this->container->get($type->getName());
        }
        if ($this->container->has($namedParam)) {
            return $this->container->get($namedParam);
        }
        throw new CanNotResolveParamException();
    }

    protected function checkParamIsRouteArgs(ReflectionParameter $param): bool
    {
        if (in_array($param->getName(), ['args', 'routeArgs', 'routeArguments'])) {
            return true;
        }
        if (is_null($param->getType())) {
            return true;
        }

        return !is_null($param->getType()) && $param->getType()->getName() === 'array';
    }


    protected function resolveParamsForClosure($callable)
    {
        $params = [];
        $methodRefl = new ReflectionClosure($callable);

        if ($methodRefl->getNumberOfParameters() > 0) {
            $maxParamIndex = $methodRefl->getNumberOfParameters() - 1;
            foreach ($methodRefl->getParameters() as $index => $param) {
                $isRouteArgs = $index === $maxParamIndex && $this->checkParamIsRouteArgs($param);

                $params[] = $this->resolveTheParamValue(
                    $param->getType(),
                    $isRouteArgs ? 'args' : $param->getName()
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
            $maxParamIndex = $funcRefl->getNumberOfParameters() - 1;
            foreach ($funcRefl->getParameters() as $index => $param) {
                $isRouteArgs = $maxParamIndex && $this->checkParamIsRouteArgs($param);

                $params[] = $this->resolveTheParamValue(
                    $param->getType(),
                    $isRouteArgs ? 'args' : $param->getName()
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
                $maxParamIndex = $methodRefl->getNumberOfParameters() - 1;
                foreach ($methodRefl->getParameters() as $index => $param) {
                    $isRouteArgs = $index === $maxParamIndex && $this->checkParamIsRouteArgs($param);
                    $params[] = $this->resolveTheParamValue(
                        $param->getType(),
                        $isRouteArgs ? 'args' : $param->getName()
                    );
                }
            }
        }

        return $params;
    }

    protected function resolveParams($callable, $request, $response, $args): array
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
            // Ignore this case
        } catch (Throwable $e) {
            // Send PHP error logs to debug
            error_log($e->getMessage());
        }
        return [$request, $response, $args];
    }
}
