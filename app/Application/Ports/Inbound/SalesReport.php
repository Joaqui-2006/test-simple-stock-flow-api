<?php

declare(strict_types=1);

namespace App\Application\Ports\Inbound;

final class SalesReport
{
    /**
     * @param array<int, SalesReportRow> $items
     */
    public function __construct(
        public readonly string $from,
        public readonly string $to,
        public readonly float $totalAmount,
        public readonly string $currency,
        public readonly array $items
    ) {
    }
}