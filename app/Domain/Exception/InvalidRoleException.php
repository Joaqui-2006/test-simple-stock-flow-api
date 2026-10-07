<?php

declare(strict_types=1);

namespace App\Domain\Exception;

final class InvalidRoleException extends BusinessRuleViolation
{
    public function __construct(string $role)
    {
        parent::__construct(sprintf('Rol no válido: "%s". Debe ser admin o seller', $role));
    }
}