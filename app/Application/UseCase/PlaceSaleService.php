<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Exception\ConcurrencyConflict;
use App\Application\Ports\Inbound\PlaceSale;
use App\Application\Ports\Inbound\PlaceSaleCommand;
use App\Application\Ports\Inbound\SaleItemView;
use App\Application\Ports\Inbound\SaleView;
use App\Application\Ports\Outbound\Clock;
use App\Application\Ports\Outbound\ProductRepository;
use App\Application\Ports\Outbound\SaleRepository;
use App\Application\Ports\Outbound\UnitOfWork;
use App\Domain\Exception\ProductNotFoundException;
use App\Domain\Model\Sale;
use App\Domain\Model\SaleItem;
use App\Domain\ValueObject\ProductId;
use App\Domain\ValueObject\Quantity;
use App\Domain\ValueObject\UserId;

final class PlaceSaleService implements PlaceSale
{
    private const MAX_RETRIES = 3;

    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly SaleRepository $saleRepository,
        private readonly UnitOfWork $unitOfWork,
        private readonly Clock $clock
    ) {
    }

    public function execute(PlaceSaleCommand $command): SaleView
    {
        $attempts = 0;

        while (true) {
            $attempts++;
            try {
                return $this->unitOfWork->run(function () use ($command): SaleView {
                    $saleItems = [];

                    foreach ($command->items as $itemData) {
                        $pid = new ProductId($itemData['productId']);
                        $quantity = new Quantity($itemData['quantity']);

                        $product = $this->productRepository->findById($pid);
                        if ($product === null || $product->isDeleted()) {
                            throw new ProductNotFoundException($itemData['productId']);
                        }

                        // Deduct stock (domain invariant RN-01)
                        $product->deductStock($quantity);
                        $this->productRepository->save($product);

                        // Freeze name and price (RN-06)
                        $saleItems[] = new SaleItem(
                            $product->getId(),
                            $product->getName(),
                            $product->getPrice(),
                            $quantity
                        );
                    }

                    $saleId = $this->saleRepository->nextIdentity();
                    $sale = new Sale(
                        $saleId,
                        new UserId($command->sellerId),
                        $command->sellerUsername,
                        $this->clock->now(),
                        $saleItems
                    );

                    $this->saleRepository->save($sale);

                    $itemViews = array_map(function (SaleItem $item): SaleItemView {
                        return new SaleItemView(
                            $item->getProductId()->toString(),
                            $item->getProductName(),
                            $item->getUnitPrice()->toFloat(),
                            $item->getQuantity()->toInt(),
                            $item->getSubtotal()->toFloat()
                        );
                    }, $sale->getItems());

                    return new SaleView(
                        $sale->getId()->toString(),
                        $sale->getSellerId()->toString(),
                        $sale->getSellerUsername(),
                        $sale->getCreatedAt()->format(DATE_ATOM),
                        $sale->getTotal()->toFloat(),
                        $sale->getTotal()->getCurrency(),
                        $itemViews
                    );
                });
            } catch (ConcurrencyConflict $e) {
                if ($attempts >= self::MAX_RETRIES) {
                    throw $e;
                }
                usleep(50000 * $attempts); // backoff
            }
        }
    }
}