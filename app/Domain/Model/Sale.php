<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Exception\EmptySaleException;
use App\Domain\Exception\RepeatedProductException;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\SaleId;
use App\Domain\ValueObject\UserId;
use DateTimeImmutable;

final class Sale
{
    /** @var array<int, SaleItem> */
    private array $items;

    /**
     * @param array<int, SaleItem> $items
     */
    public function __construct(
        private readonly SaleId $id,
        private readonly UserId $sellerId,
        private readonly string $sellerUsername,
        private readonly DateTimeImmutable $createdAt,
        array $items
    ) {
        if (empty($items)) {
            throw new EmptySaleException('Una venta debe contener al menos un producto');
        }

        $seenProductIds = [];
        foreach ($items as $item) {
            $pid = $item->getProductId()->toString();
            if (isset($seenProductIds[$pid])) {
                throw new RepeatedProductException('Un producto no puede repetirse en la misma venta');
            }
            $seenProductIds[$pid] = true;
        }

        $this->items = array_values($items);
    }

    public function getId(): SaleId
    {
        return $this->id;
    }

    public function getSellerId(): UserId
    {
        return $this->sellerId;
    }

    public function getSellerUsername(): string
    {
        return $this->sellerUsername;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return array<int, SaleItem>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getTotal(): Money
    {
        $currency = $this->items[0]->getUnitPrice()->getCurrency();
        $total = Money::zero($currency);

        foreach ($this->items as $item) {
            $total = $total->add($item->getSubtotal());
        }

        return $total;
    }
}