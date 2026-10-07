<?php

declare(strict_types=1);

namespace App\Application\Ports\Inbound;

interface Authenticate
{
    public function login(string $username, string $password): AuthResult;
    public function registerSeller(string $username, string $password): array;
}