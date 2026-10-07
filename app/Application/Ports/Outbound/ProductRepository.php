<?php

declare(strict_types=1);

namespace App\Application\Ports\Outbound;

use App\Application\Model\PageRequest;
use App\Application\Ports\Inbound\PagedResult;
use App\Domain\Model\Product;
use App\Domain\ValueObject\CategoryId;
use App\Domain\ValueObject\ProductId;

interface ProductRepository
{
    public function findById(ProductId $id): ?Product;
    public function listActive(?string $search, ?CategoryId $categoryId, PageRequest $pageRequest): PagedResult;
    public function save(Product $product): void;
    public function nextIdentity(): ProductId;
}