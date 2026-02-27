<?php

namespace App\Application\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;

class AuthMiddleware implements MiddlewareInterface {
    private $secret;

    public function __construct($settings) {
        // Line 17 fix: Check if $settings exists before accessing the index
        if (!$settings || !isset($settings['secret'])) {
            throw new \RuntimeException('JWT Secret not found in settings.');
        }
        $this->secret = $settings['secret'];
    }

    public function process(Request $request, RequestHandler $handler): Response {
        $authHeader = $request->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $authHeader);

        try {
            // Verify Token
            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
            // Optional: Pass user info forward
            $request = $request->withAttribute('admin_id', $decoded->sub);
            return $handler->handle($request);
        } catch (\Exception $e) {
            $response = new SlimResponse();
            $response->getBody()->write(json_encode(['error' => 'Unauthorized Access']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }
    }
}

