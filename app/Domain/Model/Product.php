<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Exception\InsufficientStockException;
use App\Domain\ValueObject\CategoryId;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\ProductId;
use App\Domain\ValueObject\Quantity;
use DateTimeImmutable;

final class Product
{
    public function __construct(
        private readonly ProductId $id,
        private string $name,
        private Money $price,
        private int $stock,
        private CategoryId $categoryId,
        private ?string $imageUrl = null,
        private ?DateTimeImmutable $deletedAt = null
    ) {
        if ($stock < 0) {
            throw new InsufficientStockException($stock, 0);
        }
    }

    public function getId(): ProductId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): Money
    {
        return $this->price;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function getCategoryId(): CategoryId
    {
        return $this->categoryId;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }

    public function updateDetails(string $name, Money $price, CategoryId $categoryId): void
    {
        $this->name = trim($name);
        $this->price = $price;
        $this->categoryId = $categoryId;
    }

    public function setImageUrl(?string $url): void
    {
        $this->imageUrl = $url;
    }

    public function deductStock(Quantity $quantity): void
    {
        $qty = $quantity->toInt();
        if ($this->stock < $qty) {
            throw new InsufficientStockException($this->stock, $qty);
        }
        $this->stock -= $qty;
    }

    public function markAsDeleted(DateTimeImmutable $now): void
    {
        $this->deletedAt = $now;
    }
}