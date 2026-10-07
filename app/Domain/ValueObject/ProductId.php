<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

final class ProductId
{
    public function __construct(private readonly string $value)
    {
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}