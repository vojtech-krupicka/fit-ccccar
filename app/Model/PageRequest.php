<?php declare(strict_types=1);

namespace App\Model;

final readonly class PageRequest
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $itemsPerPage = 100,
    ) {}
}
