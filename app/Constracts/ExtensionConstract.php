<?php

namespace App\Constracts;

use DI\Container;
use Slim\App;

interface ExtensionConstract
{
    public function isBuiltIn(): bool;

    public function getRoutes();

    public function setExtensionDir($extensionDir);

    public function setApp(App &$app);

    public function setContainer(Container &$container);

    public function getPriority(): int;
}
