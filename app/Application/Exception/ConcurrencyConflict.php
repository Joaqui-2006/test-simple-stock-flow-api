<?php

declare(strict_types=1);

namespace App\Application\Exception;

use RuntimeException;

final class ConcurrencyConflict extends RuntimeException
{
    public function __construct(string $message = 'Conflicto de concurrencia: el recurso fue modificado por otra transacción')
    {
        parent::__construct($message);
    }
}