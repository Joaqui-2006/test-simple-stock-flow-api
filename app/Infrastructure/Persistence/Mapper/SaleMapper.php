<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Mapper;

use App\Domain\Model\Sale;
use App\Domain\Model\SaleItem;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\ProductId;
use App\Domain\ValueObject\Quantity;
use App\Domain\ValueObject\SaleId;
use App\Domain\ValueObject\UserId;
use App\Infrastructure\Persistence\Model\SaleModel;
use DateTimeImmutable;

final class SaleMapper
{
    public static function toDomain(SaleModel $model): Sale
    {
        $items = [];
        foreach ($model->items as $itemModel) {
            $items[] = new SaleItem(
                new ProductId((string) $itemModel->product_id),
                (string) $itemModel->product_name,
                Money::fromNumber($itemModel->unit_price, (string) $itemModel->currency),
                new Quantity((int) $itemModel->quantity)
            );
        }

        $createdAt = $model->created_at !== null
            ? DateTimeImmutable::createFromMutable($model->created_at)
            : new DateTimeImmutable();

        return new Sale(
            new SaleId((string) $model->id),
            new UserId((string) $model->seller_id),
            (string) $model->seller_username,
            $createdAt,
            $items
        );
    }
}