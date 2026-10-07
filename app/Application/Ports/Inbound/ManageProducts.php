<?php

declare(strict_types=1);

namespace App\Application\Ports\Inbound;

use App\Application\Model\PageRequest;

interface ManageProducts
{
    public function listProducts(?string $search, ?string $categoryId, PageRequest $pageRequest): PagedResult;
    public function getProduct(string $id): ProductView;
    public function createProduct(string $name, float $price, string $currency, int $initialStock, string $categoryId): ProductView;
    public function updateProduct(string $id, string $name, float $price, string $currency, string $categoryId): ProductView;
    public function deleteProduct(string $id): void;
    public function setProductImage(string $id, string $binaryContent, string $mimeType): string;
    public function listCategories(): array;
    public function getMediaBinary(string $key): ?array;
}