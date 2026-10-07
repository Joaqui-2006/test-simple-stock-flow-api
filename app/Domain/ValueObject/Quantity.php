<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use App\Domain\Exception\InvalidQuantityException;

final class Quantity
{
    private int $value;

    public function __construct(int $value)
    {
        if ($value <= 0) {
            throw new InvalidQuantityException('La cantidad debe ser un entero estrictamente mayor que cero');
        }
        $this->value = $value;
    }

    public function toInt(): int
    {
        return $this->value;
    }
}