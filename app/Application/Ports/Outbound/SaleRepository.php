<?php

declare(strict_types=1);

namespace App\Application\Ports\Outbound;

use App\Application\Model\PageRequest;
use App\Application\Ports\Inbound\PagedResult;
use App\Domain\Model\Sale;
use App\Domain\ValueObject\SaleId;
use DateTimeImmutable;

interface SaleRepository
{
    public function findById(SaleId $id): ?Sale;
    public function listSales(?DateTimeImmutable $from, ?DateTimeImmutable $to, PageRequest $pageRequest): PagedResult;
    public function save(Sale $sale): void;
    public function nextIdentity(): SaleId;
}