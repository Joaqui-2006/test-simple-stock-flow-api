<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Application\Ports\Outbound\TokenGenerator;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Throwable;

final class JwtTokenGenerator implements TokenGenerator
{
    private string $secret;

    public function __construct(?string $secret = null)
    {
        $this->secret = $secret ?? (getenv('JWT_SECRET') ?: 'stockflow-secret-key-3413974');
    }

    public function generate(string $userId, string $username, string $role): string
    {
        $payload = [
            'sub' => $userId,
            'username' => $username,
            'role' => $role,
            'iat' => time(),
            'exp' => time() + (86400 * 7), // 7 days
        ];

        return JWT::encode($payload, $this->secret, 'HS256');
    }

    public function validate(string $token): ?array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
            return (array) $decoded;
        } catch (Throwable) {
            return null;
        }
    }
}