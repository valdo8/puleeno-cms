<?php

namespace App\Http;

use App\Http\Middleware\AuthGuard;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Middleware\ErrorMiddleware;
use Slim\Routing\RouteCollector;

class Kernel
{
    /**
     * @var App
     */
    private $app;

    /**
     * Constructor.
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * Configure the application.
     */
    public function configure()
    {
        $this->app->addRoutingMiddleware();
        $this->app->addErrorMiddleware(true, true, true);

        // Add your middleware here
        $this->app->add(new AuthGuard($this->app->getContainer()->get('user_repository')));

        // Add your routes here
        $this->mapRoutes();
    }

    /**
     * Map the routes.
     */
    private function mapRoutes()
    {
        $this->app->get('/', function (ServerRequestInterface $request, ResponseInterface $response) {
            return $response->withJson(['message' => 'Hello, world!']);
        });
    }
}

