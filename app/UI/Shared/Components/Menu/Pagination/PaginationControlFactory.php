<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Menu\Pagination;

interface PaginationControlFactory
{
    public function create(int $page, int $pageCount, int $totalCount, int $base = 1): PaginationControl;
}
