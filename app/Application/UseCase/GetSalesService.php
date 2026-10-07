<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Model\PageRequest;
use App\Application\Ports\Inbound\GetSales;
use App\Application\Ports\Inbound\PagedResult;
use App\Application\Ports\Inbound\SaleItemView;
use App\Application\Ports\Inbound\SaleView;
use App\Application\Ports\Outbound\SaleRepository;
use App\Domain\Exception\BusinessRuleViolation;
use App\Domain\Model\Sale;
use App\Domain\Model\SaleItem;
use App\Domain\ValueObject\SaleId;
use DateTimeImmutable;
use DomainException;

final class GetSalesService implements GetSales
{
    public function __construct(
        private readonly SaleRepository $saleRepository
    ) {
    }

    public function listSales(?DateTimeImmutable $from, ?DateTimeImmutable $to, PageRequest $pageRequest): PagedResult
    {
        return $this->saleRepository->listSales($from, $to, $pageRequest);
    }

    public function getSale(string $id): SaleView
    {
        $sale = $this->saleRepository->findById(new SaleId($id));
        if ($sale === null) {
            throw new class(sprintf('Venta no encontrada: %s', $id)) extends BusinessRuleViolation {};
        }

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
    }
}