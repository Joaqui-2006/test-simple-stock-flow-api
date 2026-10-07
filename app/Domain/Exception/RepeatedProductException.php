<?php

declare(strict_types=1);

namespace App\Domain\Exception;

final class RepeatedProductException extends BusinessRuleViolation
{
    public function __construct(string $message = 'Un producto no puede repetirse en la misma venta')
    {
        parent::__construct($message);
    }
}