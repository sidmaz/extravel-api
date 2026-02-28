<?php

declare(strict_types=1);

use App\Application\Settings\SettingsInterface;
use App\Application\Auth\AuthService;
use App\Domain\Admin\AdminRepository;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        // 1. Logger Definition
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);
            $loggerSettings = $settings->get('logger');
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },

        // 2. DATABASE CONNECTION (Required for MySqlAdminRepository)
        PDO::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);
            $dbSettings = $settings->get('db');

            $dsn = "mysql:host=" . $dbSettings['host'] . ";dbname=" . $dbSettings['database'] . ";port=" . $dbSettings['port'] . ";charset=" . $dbSettings['charset'];
            
            $pdo = new PDO($dsn, $dbSettings['username'], $dbSettings['password']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            return $pdo;
        },

        // 3. AUTH SERVICE (Fixes the #UNDEFINED# jwtSettings error)
        AuthService::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);
            return new AuthService(
                $c->get(AdminRepository::class), 
                $settings->get('jwt') // Pulls the secret/expiry from settings.php
            );
        },
    ]);
};
