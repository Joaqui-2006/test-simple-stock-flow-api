<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repository;

use App\Application\Exception\ConcurrencyConflict;
use App\Application\Model\PageRequest;
use App\Application\Ports\Inbound\PagedResult;
use App\Application\Ports\Inbound\ProductView;
use App\Application\Ports\Outbound\ProductRepository;
use App\Domain\Model\Product;
use App\Domain\ValueObject\CategoryId;
use App\Domain\ValueObject\ProductId;
use App\Infrastructure\Persistence\Mapper\ProductMapper;
use App\Infrastructure\Persistence\Model\ProductModel;
use Ramsey\Uuid\Uuid;

final class EloquentProductRepository implements ProductRepository
{
    public function findById(ProductId $id): ?Product
    {
        $model = ProductModel::find($id->toString());
        return $model !== null ? ProductMapper::toDomain($model) : null;
    }

    public function listActive(?string $search, ?CategoryId $categoryId, PageRequest $pageRequest): PagedResult
    {
        $query = ProductModel::query()
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereNull('products.deleted_at')
            ->select('products.*', 'categories.name as category_name');

        if ($search !== null && trim($search) !== '') {
            $term = '%' . strtolower(trim($search)) . '%';
            $query->whereRaw('LOWER(products.name) LIKE ?', [$term]);
        }

        if ($categoryId !== null) {
            $query->where('products.category_id', $categoryId->toString());
        }

        $total = $query->count();
        $items = $query->orderBy('products.name')
            ->offset($pageRequest->getOffset())
            ->limit($pageRequest->getPageSize())
            ->get()
            ->map(function ($row): ProductView {
                return new ProductView(
                    (string) $row->id,
                    (string) $row->name,
                    (float) $row->price,
                    (string) $row->currency,
                    (int) $row->stock,
                    (string) $row->category_id,
                    (string) $row->category_name,
                    $row->image_url !== null ? (string) $row->image_url : null
                );
            })
            ->all();

        $totalPages = (int) ceil($total / $pageRequest->getPageSize());

        return new PagedResult(
            $items,
            $total,
            $pageRequest->getPage(),
            $pageRequest->getPageSize(),
            $totalPages
        );
    }

    public function save(Product $product): void
    {
        $existing = ProductModel::find($product->getId()->toString());

        if ($existing === null) {
            ProductModel::create([
                'id' => $product->getId()->toString(),
                'name' => $product->getName(),
                'price' => $product->getPrice()->toFloat(),
                'currency' => $product->getPrice()->getCurrency(),
                'stock' => $product->getStock(),
                'category_id' => $product->getCategoryId()->toString(),
                'image_url' => $product->getImageUrl(),
                'version' => 1,
                'deleted_at' => $product->getDeletedAt()?->format('Y-m-d H:i:s'),
            ]);
            return;
        }

        // Optimistic Locking: version increment
        $currentVersion = (int) $existing->version;
        $affected = ProductModel::where('id', $product->getId()->toString())
            ->where('version', $currentVersion)
            ->update([
                'name' => $product->getName(),
                'price' => $product->getPrice()->toFloat(),
                'currency' => $product->getPrice()->getCurrency(),
                'stock' => $product->getStock(),
                'category_id' => $product->getCategoryId()->toString(),
                'image_url' => $product->getImageUrl(),
                'version' => $currentVersion + 1,
                'deleted_at' => $product->getDeletedAt()?->format('Y-m-d H:i:s'),
            ]);

        if ($affected === 0) {
            throw new ConcurrencyConflict();
        }
    }

    public function nextIdentity(): ProductId
    {
        return new ProductId(Uuid::uuid4()->toString());
    }
}