<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repository;

use App\Application\Ports\Outbound\CategoryRepository;
use App\Domain\Model\Category;
use App\Domain\ValueObject\CategoryId;
use App\Infrastructure\Persistence\Mapper\CategoryMapper;
use App\Infrastructure\Persistence\Model\CategoryModel;

final class EloquentCategoryRepository implements CategoryRepository
{
    public function findById(CategoryId $id): ?Category
    {
        $model = CategoryModel::find($id->toString());
        return $model !== null ? CategoryMapper::toDomain($model) : null;
    }

    public function findAll(): array
    {
        return CategoryModel::orderBy('name')
            ->get()
            ->map(fn(CategoryModel $m) => CategoryMapper::toDomain($m))
            ->all();
    }
}