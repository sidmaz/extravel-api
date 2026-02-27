<?php
declare(strict_types=1);

namespace App\Application\Auth;

use App\Domain\Admin\AdminRepository;
use Firebase\JWT\JWT;
use Exception;

class AuthService
{
    private AdminRepository $adminRepository;
    private array $jwtSettings;

    /**
     * We inject the INTERFACE here, keeping the service decoupled 
     * from the specific database implementation (MySQL, etc.)
     */
    public function __construct(AdminRepository $adminRepository, array $jwtSettings)
    {
        $this->adminRepository = $adminRepository;
        $this->jwtSettings = $jwtSettings;
    }

    /**
     * The core logic for checking an Admin
     * Returns a JWT string on success, or null on failure
     */
    public function authenticate(string $username, string $password): ?string
    {
        // 1. Fetch user from the repository (Infrastructure Layer)
        $user = $this->adminRepository->findAdminByUsername($username);

        // 2. Business Logic: Is the user active? Does the password match?
        if (!$user || !password_verify($password, $user['password_hash'])) {
            return null;
        }

        // 3. Logic: Prepare the Token Payload
        $payload = [
            'iss' => 'your-app-name',           // Issuer
            'iat' => time(),                    // Issued at
            'exp' => time() + $this->jwtSettings['expiry'], // Expiration
            'sub' => (string) $user['id'],      // Subject (User ID)
            'role' => $user['role']             // Custom claim for Admin role
        ];

        // 4. Generate the JWT
        return JWT::encode($payload, $this->jwtSettings['secret'], 'HS256');
    }
}