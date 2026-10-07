<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Model\DateRange;
use App\Application\Ports\Inbound\GetSalesReport;
use App\Application\Ports\Inbound\SalesReport;
use App\Application\Ports\Outbound\SalesReportQuery;

final class SalesReportService implements GetSalesReport
{
    public function __construct(
        private readonly SalesReportQuery $reportQuery
    ) {
    }

    public function getReport(DateRange $range): SalesReport
    {
        return $this->reportQuery->execute($range);
    }
}