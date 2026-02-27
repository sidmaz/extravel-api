<?php
declare(strict_types=1);

use App\Application\Actions\Auth\LoginAction; // The Action class we discussed
use App\Application\Middleware\AuthMiddleware;
use App\Application\Settings\SettingsInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {

    // 1. Setup global settings for the routes
    $container = $app->getContainer();
    $settings = $container->get(SettingsInterface::class);
    $jwtSettings = $settings->get('jwt');

    // ==========================================
    // PUBLIC ROUTES (GUEST)
    // ==========================================
    $app->get('/api/guest/home', function (Request $request, Response $response) {
        $response->getBody()->write(json_encode(['message' => 'Welcome Guest']));
        return $response->withHeader('Content-Type', 'application/json');
    });

    // ==========================================
    // AUTHENTICATION (LOGIN)
    // ==========================================
    // We point this directly to our Action class
    $app->post('/api/auth/login', LoginAction::class);

    // ==========================================
    // PROTECTED ADMIN ROUTES (GROUP)
    // ==========================================
    // Note: We use $group inside the function, NOT $app
    $app->group('/api/admin', function (Group $group) {
        
        $group->get('/employees', function (Request $request, Response $response) {
            $response->getBody()->write(json_encode(['message' => 'Welcome to Admin Dashboard']));
            return $response->withHeader('Content-Type', 'application/json');
        });

        // Add more admin routes here using $group->get or $group->post

    })->add(new AuthMiddleware($jwtSettings)); // This protects the whole group

    // ==========================================
    // CORS PREFLIGHT
    // ==========================================
    $app->options('/{routes:.+}', function (Request $request, Response $response) {
        return $response;
    });
};
