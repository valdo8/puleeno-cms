<?php

namespace App\Core;

use App\Constracts\ExtensionConstract;
use DI\Container;
use ReflectionClass;
use Slim\App;

abstract class Extension implements ExtensionConstract
{
    protected $name;

    protected $isBuiltIn = false;

    /**
     * The priority of extension
     *
     * @var integer
     */
    protected $priority = 33;

    /**
     * Slim app
     *
     * @var \App\Core\Application
     */
    protected $app;

    /**
     * @var \DI\Container
     */
    protected $container;

    protected $extensionDir;

    protected $deps = [];

    public function setExtensionName($name)
    {
        $this->name = trim($name);
    }

    public function getExtensionName()
    {
        return $this->name;
    }

    public function isBuiltIn(): bool
    {
        return boolval($this->isBuiltIn);
    }

    public function setApp(Application &$app)
    {
        $this->app = $app;
    }

    public function setContainer(Container &$container)
    {
        $this->container = $container;
    }

    public function setExtensionDir($extensionDir)
    {
        $this->extensionDir = $extensionDir;
    }

    public function addDependencyExtension($extensionName, $version = "*")
    {
        $this->deps[$extensionName] = $version;
    }

    public function getExtensionDir()
    {
        return $this->extensionDir;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function bootstrap()
    {
        //
    }

    public function setup()
    {
        //
    }

    public function registerRoutes()
    {
        $routeConfig = implode(DIRECTORY_SEPARATOR, [$this->getExtensionDir(), 'routes.php']);
        if (file_exists($routeConfig)) {
            $routes = require $routeConfig;
            if (is_callable($routes)) {
                call_user_func_array(
                    $routes,
                    [$this->app, $this->container]
                );
            }
        }
    }

    public function registerMiddlewares()
    {
        //
    }

    public function run()
    {
        //
    }

    public function getResponeCallback(): ?callable
    {
        return null;
    }
}
