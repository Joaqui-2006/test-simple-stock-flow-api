<?php

declare(strict_types=1);

namespace App\Infrastructure\Configuration;

use RuntimeException;

final class Settings
{
    public static function getRequired(string $key): string
    {
        $value = getenv($key);
        if ($value === false || trim($value) === '') {
            throw new RuntimeException(sprintf('Variable de entorno requerida no encontrada: %s', $key));
        }
        return $value;
    }
}