<?php

declare(strict_types=1);

use App\Domain\Admin\AdminRepository;
use PDO;

class MySqlAdminRepository implements AdminRepository {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function findByUsername(string $username): ?array {
        $stmt = $this->db->prepare("SELECT * FROM admin_users WHERE username = :u AND status = 'active'");
        $stmt->execute(['u' => $username]);
        $user = $stmt->fetch();
        return $user ?: null;
    }
}
