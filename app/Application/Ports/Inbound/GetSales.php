<?php

declare(strict_types=1);

namespace App\Application\Ports\Inbound;

use App\Application\Model\PageRequest;
use DateTimeImmutable;

interface GetSales
{
    public function listSales(?DateTimeImmutable $from, ?DateTimeImmutable $to, PageRequest $pageRequest): PagedResult;
    public function getSale(string $id): SaleView;
}