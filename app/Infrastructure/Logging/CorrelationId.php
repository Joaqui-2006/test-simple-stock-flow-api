<?php

declare(strict_types=1);

namespace App\Infrastructure\Logging;

use Ramsey\Uuid\Uuid;

final class CorrelationId
{
    private static ?string $current = null;

    public static function get(): string
    {
        if (self::$current === null) {
            self::$current = Uuid::uuid4()->toString();
        }
        return self::$current;
    }

    public static function set(string $id): void
    {
        self::$current = $id;
    }
}