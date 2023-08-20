<?php

namespace Puleeno;

use App\Application\Handlers\HttpErrorHandler;
use App\Application\Handlers\ShutdownHandler;
use App\Application\ResponseEmitter\ResponseEmitter;
use App\Application\Settings\SettingsInterface;
use App\Core\ExtensionManager;
use App\Core\HookManager;
use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Dotenv\Repository\Adapter\EnvConstAdapter;
use Dotenv\Repository\Adapter\PutenvAdapter;
use Dotenv\Repository\RepositoryBuilder;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;

define('ROOT_PATH', dirname(__DIR__));
define('THEMES_DIR', ROOT_PATH . DIRECTORY_SEPARATOR . 'themes');
define('CONFIGS_DIR', ROOT_PATH . DIRECTORY_SEPARATOR . 'configs');
define('RESOURCES_DIR', ROOT_PATH . DIRECTORY_SEPARATOR . 'resources');
define('STORAGES_DIR', ROOT_PATH . DIRECTORY_SEPARATOR . 'storage');
define('EXTENSIONS_DIR', ROOT_PATH . DIRECTORY_SEPARATOR . 'extensions');

final class Bootstrap
{
    /**
     * The Slim application
     *
     * @var \Slim\App
     */
    protected $app;

    /**
     * @var \DI\Container
     */
    protected $container;

    /**
     * @var \Psr\Http\Message\ServerRequestInterface
     */
    protected $request;

    protected function init()
    {
        //
    }

    protected function loadComposer()
    {
        $composerAutoloader = implode(DIRECTORY_SEPARATOR, [ROOT_PATH, 'vendor', 'autoload.php']);
        require_once $composerAutoloader;
    }

    protected function loadSetting($settingName)
    {
        $settingFile = implode(DIRECTORY_SEPARATOR, [CONFIGS_DIR, strtolower($settingName) . '.php']);
        if (file_exists($settingFile)) {
            return require $settingFile;
        }
    }

    protected function setupEnvironment()
    {
        $dotEnvFile = ROOT_PATH . DIRECTORY_SEPARATOR . '.env';
        if (file_exists($dotEnvFile)) {
            $repository = RepositoryBuilder::createWithNoAdapters()
                ->addAdapter(EnvConstAdapter::class)
                ->addWriter(PutenvAdapter::class)
                ->immutable()
                ->make();
            $dotenv = Dotenv::create($repository, ROOT_PATH);
            $dotenv->load();
        }
    }

    protected function setup()
    {
        // Instantiate PHP-DI ContainerBuilder
        $containerBuilder = new ContainerBuilder();
        if (defined('ENV_MODE') && constant('ENV_MODE')) { // Should be set to true in production
            $containerBuilder->enableCompilation(STORAGES_DIR . DIRECTORY_SEPARATOR . 'caches');
        }
        // Set up settings
        $settings = $this->loadSetting('settings');
        $settings($containerBuilder);

        // Set up dependencies
        $dependencies = $this->loadSetting('dependencies');
        $dependencies($containerBuilder);

        // Set up repositories
        $repositories = $this->loadSetting('repositories');
        $repositories($containerBuilder);

        // Instantiate the app
        AppFactory::setContainer(($this->container = $containerBuilder->build()));

        $this->app = AppFactory::create();

        // Register middleware
        $middleware = $this->loadSetting('middleware');
        $middleware($this->app);

        // Register routes
        $routes = $this->loadSetting('routes');
        $routes($this->app);

        // Added CMS version to DI
        $mainComposerFile = sprintf('%s/composer.json', ROOT_PATH);
        if (file_exists($mainComposerFile)) {
            $composerInfo = json_decode(file_get_contents($mainComposerFile), true);
            $version = isset($composerInfo['cms-version']) ? $composerInfo['cms-version'] : '0.0.0';
            $this->container->set('version', $version);
        }


        // Load extension system
        ExtensionManager::getInstance()->loadExtensions($this->app, $this->container);
    }

    public function boot()
    {
        $this->init();
        $this->loadComposer();
        $this->setupEnvironment();
        $this->setup();
        $this->run();
    }

    protected function run()
    {
        // Run all active extensions
        ExtensionManager::getInstance()->runActiveExtensions();

        $this->writeErrorLogs(
            $this->setupHttpErrorHandle()
        );

        // Run App & Emit Response
        $response = $this->app->handle($this->request);

        $responseEmitter = new ResponseEmitter();
        $responseEmitter->emit(
            HookManager::applyFilters('response', $response)
        );
    }

    // Create Error Handler
    protected function setupHttpErrorHandle()
    {
        /** @var SettingsInterface $settings */
        $settings = $this->container->get(SettingsInterface::class);

        $displayErrorDetails = $settings->get('displayErrorDetails');

        // Create Request object from globals
        $serverRequestCreator = ServerRequestCreatorFactory::create();
        $this->request = $serverRequestCreator->createServerRequestFromGlobals();

        $responseFactory = $this->app->getResponseFactory();
        $callableResolver = $this->app->getCallableResolver();
        $errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);

        // Create Shutdown Handler
        $shutdownHandler = new ShutdownHandler($this->request, $errorHandler, $displayErrorDetails);
        register_shutdown_function($shutdownHandler);

        return $errorHandler;
    }

    protected function writeErrorLogs(HttpErrorHandler $errorHandler)
    {
        $settings = $this->container->get(SettingsInterface::class);

        $displayErrorDetails = $settings->get('displayErrorDetails');
        $logError = $settings->get('logError');
        $logErrorDetails = $settings->get('logErrorDetails');

        // Add Error Middleware
        $errorMiddleware = $this->app->addErrorMiddleware($displayErrorDetails, $logError, $logErrorDetails);
        $errorMiddleware->setDefaultErrorHandler($errorHandler);
    }

    public function getApp(): App
    {
        return $this->app;
    }
}
