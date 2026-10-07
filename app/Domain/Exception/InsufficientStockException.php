<?php

declare(strict_types=1);

namespace App\Domain\Exception;

final class InsufficientStockException extends BusinessRuleViolation
{
    public function __construct(int $available, int $requested)
    {
        parent::__construct(
            sprintf('El stock disponible (%d) es insuficiente para la cantidad solicitada (%d)', $available, $requested)
        );
    }
}