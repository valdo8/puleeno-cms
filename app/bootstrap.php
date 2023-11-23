<?php

namespace Puleeno;

use App\Common\Option;
use App\Constracts\AssetTypeEnum;
use App\Core\AssetManager;
use App\Core\Assets\AssetStylesheetOptions;
use App\Core\Assets\AssetUrl;
use App\Http\Controllers\GlobalController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Core\ExtensionManager;
use App\Core\Factory\AppFactory;
use App\Core\Helper;
use App\Core\HookManager;
use App\Core\Settings\SettingsInterface;
use App\Http\Handlers\HttpErrorHandler;
use App\Http\Handlers\ShutdownHandler;
use App\Http\ResponseEmitter\ResponseEmitter;
use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Dotenv\Repository\Adapter\EnvConstAdapter;
use Dotenv\Repository\Adapter\PutenvAdapter;
use Dotenv\Repository\RepositoryBuilder;
use ReflectionClass;
use Slim\App;
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

    protected static $instance;

    protected ExtensionManager $extensionManager;

    protected function __construct()
    {
    }

    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new self();
        }
        return static::$instance;
    }

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

        // Init extension manager
        $this->extensionManager = ExtensionManager::getInstance();

        // Init asset type enum
        AssetTypeEnum::init();
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

        $this->app = AppFactory::createApp();

        // Register middleware
        $middleware = $this->loadSetting('middleware');
        $middleware($this->app);

        // Register routes
        $routes = require __DIR__ . '/../configs/routes.php';
        $routes($this->app);

        // Register routes
        $this->app->options('/{routes:.*}', function (Request $request, Response $response) {
            // CORS Pre-Flight OPTIONS Request Handler
            return $response;
        });

        // Added CMS version to DI
        $mainComposerFile = sprintf('%s/composer.json', ROOT_PATH);
        if (file_exists($mainComposerFile)) {
            $composerInfo = json_decode(file_get_contents($mainComposerFile), true);
            $version = isset($composerInfo['cms-version']) ? $composerInfo['cms-version'] : '0.0.0';
            $this->container->set('version', $version);
        }

        $this->container->set('option', Option::getInstance());
    }

    protected function initAssets()
    {
        AssetManager::registerAsset(
            'sakura-css',
            new AssetUrl('/assets/vendors/sakura/css/sakura.css'),
            AssetTypeEnum::CSS(),
            [],
            '1.5.0',
            AssetStylesheetOptions::parseOptionFromArray([]),
            1
        )->enqueue();
    }

    protected function initExtensions()
    {
        // Load extension system
        $this->extensionManager->init($this->app, $this->container);
    }

    public function boot()
    {
        $this->init();
        $this->loadComposer();
        $this->setupEnvironment();
        $this->setup();

        $this->writeErrorLogs(
            $this->setupHttpErrorHandle()
        );

        $this->initAssets();
        $this->initExtensions();
        $this->loadExtensions();
        $this->run();
    }

    protected function loadExtensions()
    {
        // Execute loaded extensions hooks
        HookManager::executeAction('loaded_extensions');

        // Run all active extensions
        $this->extensionManager->runActiveExtensions();
    }

    protected function setupAssets()
    {
        // Setup assets after extensions are loaded
        $assetManager = AssetManager::getInstance();
        $version = $this->container->get('version');
        HookManager::addAction('head', function () use ($version) {
            echo sprintf('<meta name="generator" content="Puleeno CMS %s" />', $version) . PHP_EOL;
        }, 0);
        HookManager::addAction('head', function () {
            $faviconUrl = HookManager::applyFilters('favicon_url', '/assets/favicon.ico');
            echo sprintf(str_repeat("\t", 2) . '<link rel="icon" type="image/x-icon" href="%s">', $faviconUrl) . PHP_EOL;
        });

        // Setup assets in <head> tag
        HookManager::addAction('head', [$assetManager, 'printInitHeadScripts'], 33);
        HookManager::addAction('head', [$assetManager, 'printHeadAssets'], 66);
        HookManager::addAction('head', [$assetManager, 'printExecuteHeadScripts'], 99);

        // Setup asset before </body> tag
        HookManager::addAction('footer', [$assetManager, 'printFooterInitScripts'], 33);
        HookManager::addAction('footer', [$assetManager, 'printFooterAssets'], 66);
        HookManager::addAction('footer', [$assetManager, 'executeFooterScripts'], 99);
    }


    /**
     * This method use to register actions to URL has format `/pagepath`
     */
    protected function registerGlobalController()
    {
        $this->app->any('/{pagePath:/?.+}', [GlobalController::class, 'handle']);
    }

    protected function run()
    {
        $this->setupAssets();

        $this->registerGlobalController();

        $themeBootstrap = implode(DIRECTORY_SEPARATOR, [get_path('theme'), get_active_theme(), 'bootstrap.php']);
        if (file_exists($themeBootstrap)) {
            require_once $themeBootstrap;
        }

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

        $requestPath = $this->request->getUri() != null ? $this->request->getUri()->getPath() : '/';
        $isDashboard = $requestPath === $settings->get('admin_prefix', '/dashboard')
            || strpos($requestPath, $settings->get('admin_prefix', '/dashboard') . '/') === 0;

        $this->container->set('is_dashboard', $isDashboard);

        $this->setupDashboardEnvironment($isDashboard);

        $responseFactory = $this->app->getResponseFactory();
        $callableResolver = $this->app->getCallableResolver();
        $errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);

        // Create Shutdown Handler
        $shutdownHandler = new ShutdownHandler($this->request, $errorHandler, $displayErrorDetails);
        register_shutdown_function($shutdownHandler);

        return $errorHandler;
    }

    protected function setupDashboardEnvironment($isDashboard)
    {
        $helperRelf = new ReflectionClass(Helper::class);
        $isDashboardProperty = $helperRelf->getProperty('isDashboard');
        $isDashboardProperty->setAccessible(true);
        $isDashboardProperty->setValue($isDashboardProperty, $isDashboard);
        $isDashboardProperty->setAccessible(false);
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
