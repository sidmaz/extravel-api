<?php
declare(strict_types=1);

namespace App\Application\Actions\Auth;

use App\Application\Auth\AuthService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class LoginAction
{
    private AuthService $authService;

    // Inject the Service Layer here
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * The __invoke method makes the class "callable"
     */
    public function __invoke(Request $request, Response $response): Response
    {
        // 1. Get data from React (Frontend)
        $data = $request->getParsedBody();
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';

        // 2. Call the Service Layer (Business Logic)
        $token = $this->authService->authenticate($username, $password);

        // 3. Handle Failure
        if (!$token) {
            $payload = [
                'error' => '1',
                'detail' => 'Invalid Credentials' // Matches your requested typo
            ];
            $response->getBody()->write(json_encode($payload));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        // 4. Handle Success
        $response->getBody()->write(json_encode(['token' => $token]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
