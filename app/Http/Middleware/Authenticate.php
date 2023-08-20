<?php

namespace App\Http\Middleware;

use App\Constracts\AuthenticateMiddlewareConstract;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Authenticate implements MiddlewareInterface, AuthenticateMiddlewareConstract
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        // return $response->withHeader('Location', 'https://www.example.com')
        //     ->withStatus(302);

        return $response;
    }
}
