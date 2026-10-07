<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use InvalidArgumentException;

final class Username
{
    private string $value;

    public function __construct(string $raw)
    {
        $normalized = strtolower(trim($raw));
        if ($normalized === '') {
            throw new InvalidArgumentException('El nombre de usuario no puede estar vacÃ­o');
        }
        $this->value = $normalized;
    }

    public function toString(): string
    {
        return $this->value;
    }
}