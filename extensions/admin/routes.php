<?php

declare(strict_types=1);

use App\Application\Middleware\SessionMiddleware;
use App\Application\Settings\SettingsInterface;
use App\Http\Middleware\Authenticate;
use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {

    /** @var SettingsInterface $settings */
    $settings = $this->container->get(SettingsInterface::class);
    $admin_prefix = $settings->get('admin_prefix', 'dashboard');

    $app->group($admin_prefix, function (RouteCollectorProxy $group) use ($app) {
        $app->add(new Authenticate());

        $group->map(['GET', 'DELETE', 'PATCH', 'PUT'], '', function ($request, Response $response, array $args) {
            return $response;
        })->setName('user');

        $group->get('/reset-password', function ($request, $response, array $args) {
            // Route for /users/{id:[0-9]+}/reset-password
            // Reset the password for user identified by $args['id']
            // ...

            return $response;
        })->setName('user-password-reset');

        $group->get('/test/{id:[0-9]+}', function ($request, $response, array $args) {
            // Route for /invoice/{id:[0-9]+}

            var_dump($args);
            die;
            return $response;
        });
    });
};
