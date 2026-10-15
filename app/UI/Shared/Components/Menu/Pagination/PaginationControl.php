<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Menu\Pagination;

use Nette\Application\UI\Control;

class PaginationControl extends Control
{
    public function __construct(
        private int $page,
        private readonly int $pageCount,
        private readonly int $totalCount,
        private readonly int $base = 1,
    ) {
        if ($this->page < 1) {
            $this->page = 1;
        }
        if ($this->page > $this->pageCount) {
            $this->page = $this->pageCount;
        }
        if ($this->base < 1) {
            $this->base = 1;
        }
    }

    public function render(): void
    {
        $this->template->items = $this->generateItems();
        $this->template->render(__DIR__ . '/pagination.latte');
    }

    #region Getters

    public function isActive(int $page): bool
    {
        return $this->getPage() === $page;
    }

    public function isFirst(): bool
    {
        return $this->getPage() === $this->getBase();
    }

    public function getFirstPage(): int
    {
        return $this->getBase();
    }

    public function getPreviousPage(): int
    {
        return max($this->getBase(), $this->getPage() - 1);
    }

    public function isLast(): bool
    {
        return $this->getPage() === $this->getBase() + $this->getPageCount() - 1;
    }

    public function getLastPage(): int
    {
        return $this->getBase() + $this->getPageCount() - 1;
    }

    public function getNextPage(): int
    {
        return min($this->getLastPage(), $this->getPage() + 1);
    }

    public function getPage(): int
    {
        return $this->base + $this->page - 1;
    }

    public function getPageCount(): int
    {
        return $this->pageCount;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    public function getBase(): int
    {
        return $this->base;
    }

    #endregion

    private function generateItems(): array
    {
        // $items = [];
        // for ($i = $this->getBase(); $i <= $this->getLastPage(); $i++) {
        //     $items[] = $i;
        // }
        // return $items;

        $count = 3;
        $steps = array();
        if ($this->getPageCount() > 1) {
            $offset = $left = $right = floor($count / 2);
            if ($this->getPage() <= $offset && $this->getPage() < $this->getFirstPage() - 1 + $count) {
                $right = $count - $this->getPage();
            }
            if ($this->getPage() >= $this->getLastPage() - $offset && $this->getPage() > $this->getLastPage() - 1 - $count) {
                $left = $this->getPage() - ($this->getLastPage() - $count) - 1;
            }

            if ($this->getPage() - $left > $this->getFirstPage()) {
                $steps[] = $this->getFirstPage();
            }

            $steps = array_merge($steps, range(max($this->getFirstPage(), $this->getPage() - $left), min($this->getLastPage(), $this->getPage() + $right)));

            if ($this->getPage() + $right < $this->getLastPage()) {
                $steps[] = $this->getLastPage();
            }
        }
        return $steps;
    }
}
