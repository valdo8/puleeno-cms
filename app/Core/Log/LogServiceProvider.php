<?php

namespace App\Core\Log;

use App\Core\Helper;
use App\Core\Settings\SettingsInterface;
use App\Http\Handlers\HttpErrorHandler;
use App\Http\Handlers\ShutdownHandler;
use App\Providers\ServiceProvider;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use Slim\Factory\ServerRequestCreatorFactory;

class LogServiceProvider extends ServiceProvider
{
    protected function setupDashboardEnvironment($isDashboard)
    {
        $helperRelf = new ReflectionClass(Helper::class);
        $isDashboardProperty = $helperRelf->getProperty('isDashboard');
        $isDashboardProperty->setAccessible(true);
        $isDashboardProperty->setValue($isDashboardProperty, $isDashboard);
        $isDashboardProperty->setAccessible(false);
    }

    protected function setupHttpErrorHandle()
    {
        /**
         * @var ContainerInterface
         */
        $container = $this->app->getContainer();

        /** @var SettingsInterface $settings */
        $settings = $container->get(SettingsInterface::class);

        $displayErrorDetails = $settings->get('displayErrorDetails');

        $request = $container->has('request')
            ? $container->get('request')
            : null;

        if (is_null($request)) {
            // Create Request object from globals
            $serverRequestCreator = ServerRequestCreatorFactory::create();
            $request = $serverRequestCreator->createServerRequestFromGlobals();
        }

        $requestPath = $request->getUri() != null ? $request->getUri()->getPath() : '/';
        $isDashboard = $requestPath === $settings->get('admin_prefix', '/dashboard')
            || strpos($requestPath, $settings->get('admin_prefix', '/dashboard') . '/') === 0;

        $container->set('is_dashboard', $isDashboard);

        $this->setupDashboardEnvironment($isDashboard);

        $responseFactory = $this->app->getResponseFactory();
        $callableResolver = $this->app->getCallableResolver();
        $errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);

        // Create Shutdown Handler
        $shutdownHandler = new ShutdownHandler($request, $errorHandler, $displayErrorDetails);
        register_shutdown_function($shutdownHandler);

        return $errorHandler;
    }

    protected function writeErrorLogs(HttpErrorHandler $errorHandler)
    {
        /**
         * @var ContainerInterface
         */
        $container = $this->app->getContainer();
        $settings = $container->get(SettingsInterface::class);

        $displayErrorDetails = $settings->get('displayErrorDetails');
        $logError = $settings->get('logError');
        $logErrorDetails = $settings->get('logErrorDetails');

        // Add Error Middleware
        $errorMiddleware = $this->app->addErrorMiddleware($displayErrorDetails, $logError, $logErrorDetails);
        $errorMiddleware->setDefaultErrorHandler($errorHandler);
    }

    public function register()
    {
        $this->writeErrorLogs(
            $this->setupHttpErrorHandle()
        );
    }

    public function boot()
    {
        die('zo');
    }
}
