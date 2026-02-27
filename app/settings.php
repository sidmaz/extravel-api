<?php

declare(strict_types=1);

use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {

    // Global Settings Object
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            return new Settings([
                'displayErrorDetails' => true, // Should be set to false in production
                'logError'            => false,
                'logErrorDetails'     => false,
                'logger' => [
                    'name' => 'slim-app',
                    'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                    'level' => Logger::DEBUG,
                ],
                // --- ENSURE THIS "jwt" KEY EXISTS HERE ---
                'jwt' => [
                    'secret' => 'REPLACE_WITH_A_LONG_RANDOM_STRING', 
                    'expiry' => 3600,
                ],
                // --- ADD YOUR DB SETTINGS HERE ---
                'db' => [
                    'host' => 'localhost',
                    'database' => 'testdb',
                    'username' => 'root',
                    'password' => 'root',
                    'charset' => 'utf8mb4',
                ],
            ]);
        }
    ]);
};
