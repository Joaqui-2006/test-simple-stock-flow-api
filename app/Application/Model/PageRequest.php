<?php

declare(strict_types=1);

namespace App\Application\Model;

final class PageRequest
{
    private int $page;
    private int $pageSize;

    public function __construct(int $page = 1, int $pageSize = 20)
    {
        $this->page = max(1, $page);
        $this->pageSize = min(100, max(1, $pageSize));
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function getOffset(): int
    {
        return ($this->page - 1) * $this->pageSize;
    }
}