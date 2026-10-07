<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\ValueObject\CategoryId;

final class Category
{
    public function __construct(
        private readonly CategoryId $id,
        private readonly string $name
    ) {
    }

    public function getId(): CategoryId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}