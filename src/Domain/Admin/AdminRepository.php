<?php

declare(strict_types=1);

namespace App\Domain\Admin;

interface AdminRepository
{
    /**
     * @return User[]
     */
    public function findAdminByUsername(string $username): ?array;

    
}
