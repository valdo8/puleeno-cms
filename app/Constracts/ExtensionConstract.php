<?php

namespace App\Constracts;

use DI\Container;
use Slim\App;

interface ExtensionConstract
{
    public function setExtensionName($name);

    public function getExtensionName();

    public function isBuiltIn(): bool;

    public function setExtensionDir($extensionDir);

    public function setApp(App &$app);

    public function setContainer(Container &$container);

    public function getPriority(): int;

    public function bootstrap();

    public function setup();

    public function registerRoutes();

    public function registerMiddlewares();

    public function run();
}
