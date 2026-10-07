<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repository;

use App\Application\Model\PageRequest;
use App\Application\Ports\Inbound\PagedResult;
use App\Application\Ports\Inbound\SaleItemView;
use App\Application\Ports\Inbound\SaleView;
use App\Application\Ports\Outbound\SaleRepository;
use App\Domain\Model\Sale;
use App\Domain\ValueObject\SaleId;
use App\Infrastructure\Persistence\Mapper\SaleMapper;
use App\Infrastructure\Persistence\Model\SaleItemModel;
use App\Infrastructure\Persistence\Model\SaleModel;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;

final class EloquentSaleRepository implements SaleRepository
{
    public function findById(SaleId $id): ?Sale
    {
        $model = SaleModel::with('items')->find($id->toString());
        return $model !== null ? SaleMapper::toDomain($model) : null;
    }

    public function listSales(?DateTimeImmutable $from, ?DateTimeImmutable $to, PageRequest $pageRequest): PagedResult
    {
        $query = SaleModel::with('items');

        if ($from !== null) {
            $query->where('created_at', '>=', $from->format('Y-m-d H:i:s'));
        }
        if ($to !== null) {
            $query->where('created_at', '<', $to->format('Y-m-d H:i:s'));
        }

        $total = $query->count();
        $models = $query->orderByDesc('created_at')
            ->offset($pageRequest->getOffset())
            ->limit($pageRequest->getPageSize())
            ->get();

        $items = $models->map(function (SaleModel $m): SaleView {
            $sale = SaleMapper::toDomain($m);
            $itemViews = array_map(fn($item) => new SaleItemView(
                $item->getProductId()->toString(),
                $item->getProductName(),
                $item->getUnitPrice()->toFloat(),
                $item->getQuantity()->toInt(),
                $item->getSubtotal()->toFloat()
            ), $sale->getItems());

            return new SaleView(
                $sale->getId()->toString(),
                $sale->getSellerId()->toString(),
                $sale->getSellerUsername(),
                $sale->getCreatedAt()->format(DATE_ATOM),
                $sale->getTotal()->toFloat(),
                $sale->getTotal()->getCurrency(),
                $itemViews
            );
        })->all();

        $totalPages = (int) ceil($total / $pageRequest->getPageSize());

        return new PagedResult(
            $items,
            $total,
            $pageRequest->getPage(),
            $pageRequest->getPageSize(),
            $totalPages
        );
    }

    public function save(Sale $sale): void
    {
        SaleModel::create([
            'id' => $sale->getId()->toString(),
            'seller_id' => $sale->getSellerId()->toString(),
            'seller_username' => $sale->getSellerUsername(),
            'created_at' => $sale->getCreatedAt()->format('Y-m-d H:i:s'),
        ]);

        foreach ($sale->getItems() as $item) {
            SaleItemModel::create([
                'id' => Uuid::uuid4()->toString(),
                'sale_id' => $sale->getId()->toString(),
                'product_id' => $item->getProductId()->toString(),
                'product_name' => $item->getProductName(),
                'unit_price' => $item->getUnitPrice()->toFloat(),
                'currency' => $item->getUnitPrice()->getCurrency(),
                'quantity' => $item->getQuantity()->toInt(),
            ]);
        }
    }

    public function nextIdentity(): SaleId
    {
        return new SaleId(Uuid::uuid4()->toString());
    }
}