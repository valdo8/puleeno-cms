<?php

namespace App\Constracts;

use App\Core\Application;
use DI\Container;

interface ExtensionConstract
{
    public function setExtensionName($name);

    public function getExtensionName();

    public function isBuiltIn(): bool;

    public function setExtensionDir($extensionDir);

    public function setApp(Application &$app);

    public function setContainer(Container &$container);

    public function getPriority(): int;

    public function bootstrap();

    public function setup();

    public function getResponeCallback(): ?callable;

    public function registerRoutes();

    public function registerMiddlewares();

    public function run();
}
