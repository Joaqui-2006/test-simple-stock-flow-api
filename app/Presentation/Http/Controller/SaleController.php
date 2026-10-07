<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller;

use App\Application\Model\PageRequest;
use App\Application\Ports\Inbound\GetSales;
use App\Application\Ports\Inbound\PlaceSale;
use App\Application\Ports\Inbound\PlaceSaleCommand;
use App\Presentation\Http\Request\PlaceSaleRequest;
use DateTimeImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class SaleController
{
    public function __construct(
        private readonly PlaceSale $placeSaleService,
        private readonly GetSales $getSalesService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $page = (int) $request->query('page', 1);
        $pageSize = (int) $request->query('pageSize', 20);

        $from = $request->query('from') ? new DateTimeImmutable((string) $request->query('from')) : null;
        $to = $request->query('to') ? new DateTimeImmutable((string) $request->query('to')) : null;

        $result = $this->getSalesService->listSales($from, $to, new PageRequest($page, $pageSize));

        return response()->json([
            'items' => array_map(fn($s) => [
                'id' => $s->id,
                'sellerId' => $s->sellerId,
                'sellerUsername' => $s->sellerUsername,
                'createdAt' => $s->createdAt,
                'total' => $s->total,
                'currency' => $s->currency,
                'items' => array_map(fn($item) => [
                    'productId' => $item->productId,
                    'productName' => $item->productName,
                    'unitPrice' => $item->unitPrice,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->subtotal,
                ], $s->items),
            ], $result->items),
            'total' => $result->total,
            'page' => $result->page,
            'pageSize' => $result->pageSize,
            'totalPages' => $result->totalPages,
        ], 200);
    }

    public function show(string $id): JsonResponse
    {
        $s = $this->getSalesService->getSale($id);
        return response()->json([
            'id' => $s->id,
            'sellerId' => $s->sellerId,
            'sellerUsername' => $s->sellerUsername,
            'createdAt' => $s->createdAt,
            'total' => $s->total,
            'currency' => $s->currency,
            'items' => array_map(fn($item) => [
                'productId' => $item->productId,
                'productName' => $item->productName,
                'unitPrice' => $item->unitPrice,
                'quantity' => $item->quantity,
                'subtotal' => $item->subtotal,
            ], $s->items),
        ], 200);
    }

    public function store(PlaceSaleRequest $request): JsonResponse
    {
        $sellerId = (string) $request->attributes->get('auth_user_id', '');
        $sellerUsername = (string) $request->attributes->get('auth_username', '');

        $items = (array) $request->validated('items');

        $command = new PlaceSaleCommand(
            $sellerId,
            $sellerUsername,
            $items
        );

        $s = $this->placeSaleService->execute($command);

        return response()->json([
            'id' => $s->id,
            'sellerId' => $s->sellerId,
            'sellerUsername' => $s->sellerUsername,
            'createdAt' => $s->createdAt,
            'total' => $s->total,
            'currency' => $s->currency,
            'items' => array_map(fn($item) => [
                'productId' => $item->productId,
                'productName' => $item->productName,
                'unitPrice' => $item->unitPrice,
                'quantity' => $item->quantity,
                'subtotal' => $item->subtotal,
            ], $s->items),
        ], 201);
    }
}