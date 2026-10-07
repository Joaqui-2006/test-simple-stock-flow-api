<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Mapper;

use App\Domain\Model\Product;
use App\Domain\ValueObject\CategoryId;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\ProductId;
use App\Infrastructure\Persistence\Model\ProductModel;
use DateTimeImmutable;

final class ProductMapper
{
    public static function toDomain(ProductModel $model): Product
    {
        $deletedAt = $model->deleted_at !== null
            ? DateTimeImmutable::createFromMutable($model->deleted_at)
            : null;

        return new Product(
            new ProductId((string) $model->id),
            (string) $model->name,
            Money::fromNumber($model->price, (string) $model->currency),
            (int) $model->stock,
            new CategoryId((string) $model->category_id),
            $model->image_url !== null ? (string) $model->image_url : null,
            $deletedAt
        );
    }
}