<?php

declare(strict_types=1);

namespace App\Application\Ports\Inbound;

final class PlaceSaleCommand
{
    /**
     * @param array<int, array{productId: string, quantity: int}> $items
     */
    public function __construct(
        public readonly string $sellerId,
        public readonly string $sellerUsername,
        public readonly array $items
    ) {
    }
}