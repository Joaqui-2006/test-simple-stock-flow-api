<?php

declare(strict_types=1);

namespace App\Domain\Exception;

final class InvalidCredentialsException extends BusinessRuleViolation
{
    public function __construct(string $message = 'Credenciales de acceso no válidas')
    {
        parent::__construct($message);
    }
}