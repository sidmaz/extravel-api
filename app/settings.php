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
                'logError'            => true,
                'logErrorDetails'     => true,
                'logger' => [
                    'name' => 'slim-app',
                    // Use realpath to ensure Windows understands the directory
                    'path' => realpath(__DIR__ . '/../') . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'app.log',
                    'level' => \Monolog\Logger::DEBUG,
                ],
                // --- ENSURE THIS "jwt" KEY EXISTS HERE ---
                'jwt' => [
                    'secret' => 'REPLACE_WITH_A_LONG_RANDOM_STRING', 
                    'expiry' => 3600,
                ],
                // --- ADD YOUR DB SETTINGS HERE ---
                'db' => [
                    'host' => '127.0.0.1',
                    'port' => '3306' ,
                    'database' => 'testdb',
                    'username' => 'root',
                    'password' => 'root',
                    'charset' => 'utf8mb4',
                ],
            ]);
        }
    ]);
};
