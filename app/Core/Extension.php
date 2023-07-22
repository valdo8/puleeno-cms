<?php

namespace App\Core;

use App\Constracts\ExtensionConstract;
use DI\Container;
use Slim\App;

abstract class Extension implements ExtensionConstract
{
    protected $isBuiltIn = false;

    /**
     * Slim app
     *
     * @var \Slim\App
     */
    protected $app;

    /**
     * @var \DI\Container
     */
    protected $container;

    protected $extensionDir;

    public function isBuiltIn(): bool
    {
        return boolval($this->isBuiltIn);
    }

    public function setApp(App &$app)
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

    public function getExtensionDir()
    {
        return $this->extensionDir;
    }

    public function getRoutes()
    {
        $routeConfig = implode(DIRECTORY_SEPARATOR, [$this->getExtensionDir(), 'routes.php']);
        if (file_exists($routeConfig)) {
            $routes = require $routeConfig;
            if (is_callable($routes)) {
                $routes($this->app);
            }
        }
    }
}
