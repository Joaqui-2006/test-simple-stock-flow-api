<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller;

use App\Application\Model\DateRange;
use App\Application\Ports\Inbound\GetSalesReport;
use DateTimeImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ReportController
{
    public function __construct(
        private readonly GetSalesReport $reportService
    ) {
    }

    public function sales(Request $request): JsonResponse
    {
        $fromStr = (string) $request->query('from', '');
        $toStr = (string) $request->query('to', '');

        if ($fromStr === '' || $toStr === '') {
            return response()->json([
                'type' => 'about:blank',
                'title' => 'Bad Request',
                'status' => 400,
                'detail' => 'Los parÃ¡metros from y to son obligatorios para consultar el reporte',
                'errors' => [
                    'from' => ['Fecha inicial requerida en formato ISO'],
                    'to' => ['Fecha final requerida en formato ISO']
                ]
            ], 400, ['Content-Type' => 'application/problem+json']);
        }

        $from = new DateTimeImmutable($fromStr);
        $to = new DateTimeImmutable($toStr);
        $range = new DateRange($from, $to);

        $report = $this->reportService->getReport($range);

        return response()->json([
            'from' => $report->from,
            'to' => $report->to,
            'totalAmount' => $report->totalAmount,
            'currency' => $report->currency,
            'items' => array_map(fn($row) => [
                'productId' => $row->productId,
                'productName' => $row->productName,
                'unitsSold' => $row->unitsSold,
                'totalAmount' => $row->totalAmount,
            ], $report->items),
        ], 200);
    }
}