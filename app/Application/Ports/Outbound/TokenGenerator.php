<?php

declare(strict_types=1);

namespace App\Application\Ports\Outbound;

interface TokenGenerator
{
    public function generate(string $userId, string $username, string $role): string;
    public function validate(string $token): ?array;
}