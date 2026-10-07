<?php

declare(strict_types=1);

namespace App\Application\Ports\Inbound;

final class AuthResult
{
    public function __construct(
        public readonly string $token,
        public readonly string $userId,
        public readonly string $username,
        public readonly string $role
    ) {
    }
}