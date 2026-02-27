<?php

declare(strict_types=1);

use App\Application\Middleware\SessionMiddleware;
use App\Application\Middleware\CorsMiddleware;
use Slim\App;

return function (App $app) {
    //$app->add(SessionMiddleware::class);
// 1. Handle JSON/Forms from React
    $app->addBodyParsingMiddleware();

// 2. Add your custom CORS middleware
    $app->add(CorsMiddleware::class);
    
// 3. Add Routing and Error Middleware (standard Slim)
    $app->addRoutingMiddleware();
};
