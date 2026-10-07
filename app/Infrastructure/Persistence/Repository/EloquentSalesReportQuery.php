<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repository;

use App\Application\Model\DateRange;
use App\Application\Ports\Inbound\SalesReport;
use App\Application\Ports\Inbound\SalesReportRow;
use App\Application\Ports\Outbound\SalesReportQuery;
use Illuminate\Support\Facades\DB;

final class EloquentSalesReportQuery implements SalesReportQuery
{
    public function execute(DateRange $range): SalesReport
    {
        $fromStr = $range->getFrom()->format('Y-m-d H:i:s');
        $toStr = $range->getTo()->format('Y-m-d H:i:s');

        // Aggregation in database engine according to ADR-004 & DP-01
        // Most recent frozen name in the range
        $rows = DB::select("
            SELECT 
                si.product_id,
                (
                    SELECT sub_si.product_name 
                    FROM sale_items sub_si
                    JOIN sales sub_s ON sub_si.sale_id = sub_s.id
                    WHERE sub_si.product_id = si.product_id
                      AND sub_s.created_at >= ?
                      AND sub_s.created_at < ?
                    ORDER BY sub_s.created_at DESC, sub_si.id DESC
                    LIMIT 1
                ) AS product_name,
                SUM(si.quantity) AS units_sold,
                SUM(si.unit_price * si.quantity) AS total_amount
            FROM sale_items si
            JOIN sales s ON si.sale_id = s.id
            WHERE s.created_at >= ?
              AND s.created_at < ?
            GROUP BY si.product_id
            ORDER BY total_amount DESC
        ", [$fromStr, $toStr, $fromStr, $toStr]);

        $items = [];
        $totalAmount = 0.0;

        foreach ($rows as $row) {
            $amount = (float) $row->total_amount;
            $totalAmount += $amount;
            $items[] = new SalesReportRow(
                (string) $row->product_id,
                (string) $row->product_name,
                (int) $row->units_sold,
                $amount
            );
        }

        return new SalesReport(
            $range->getFrom()->format(DATE_ATOM),
            $range->getTo()->format(DATE_ATOM),
            $totalAmount,
            'COP',
            $items
        );
    }
}