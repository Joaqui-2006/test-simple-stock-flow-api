<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

final class CategoryId
{
    public function __construct(private readonly string $value)
    {
    }

    public function toString(): string
    {
        return $this->value;
    }
}