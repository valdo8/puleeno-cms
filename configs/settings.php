<?php

declare(strict_types=1);

use App\Core\Settings\Settings;
use App\Core\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {
    // Global Settings Object
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            return new Settings([
                'displayErrorDetails' => boolval(getenv('DEBUG')), // Should be set to false in production
                'logError'            => false,
                'logErrorDetails'     => false,
                'logger' => [
                    'name' => 'slim-app',
                    'path' => isset($_ENV['docker']) ? 'php://stdout' : implode(DIRECTORY_SEPARATOR, [get_path('storage'), 'logs', 'app.log']),
                    'level' => Logger::DEBUG,
                ],
                'admin_prefix' => '/dashboard',
            ]);
        }
    ]);
};
