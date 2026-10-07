<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Mapper;

use App\Domain\Model\Category;
use App\Domain\ValueObject\CategoryId;
use App\Infrastructure\Persistence\Model\CategoryModel;

final class CategoryMapper
{
    public static function toDomain(CategoryModel $model): Category
    {
        return new Category(
            new CategoryId((string) $model->id),
            (string) $model->name
        );
    }
}