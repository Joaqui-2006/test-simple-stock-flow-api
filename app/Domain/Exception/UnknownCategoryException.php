<?php

declare(strict_types=1);

namespace App\Domain\Exception;

final class UnknownCategoryException extends BusinessRuleViolation
{
    public function __construct(string $id)
    {
        parent::__construct(sprintf('Categoría desconocida o no válida: %s', $id));
    }
}