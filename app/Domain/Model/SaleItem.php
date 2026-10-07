<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\ProductId;
use App\Domain\ValueObject\Quantity;

final class SaleItem
{
    public function __construct(
        private readonly ProductId $productId,
        private readonly string $productName,
        private readonly Money $unitPrice,
        private readonly Quantity $quantity
    ) {
    }

    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function getUnitPrice(): Money
    {
        return $this->unitPrice;
    }

    public function getQuantity(): Quantity
    {
        return $this->quantity;
    }

    public function getSubtotal(): Money
    {
        return $this->unitPrice->multiply($this->quantity->toInt());
    }
}