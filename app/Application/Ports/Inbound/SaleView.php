<?php

declare(strict_types=1);

namespace App\Application\Ports\Inbound;

final class SaleView
{
    /**
     * @param array<int, SaleItemView> $items
     */
    public function __construct(
        public readonly string $id,
        public readonly string $sellerId,
        public readonly string $sellerUsername,
        public readonly string $createdAt,
        public readonly float $total,
        public readonly string $currency,
        public readonly array $items
    ) {
    }
}