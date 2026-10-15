<?php declare(strict_types=1);

namespace App\Model;

use Nette\Database\Table\Selection;

/**
 * @template T of BaseEntity
 */
class EntityList
{
    /**
     * @param list<T> $entities
     * @param int $totalCount
     * @param int|null $currentPage
     * @param int|null $pageCount
     */
    public function __construct(
        public readonly array $entities,
        public readonly int $totalCount,
        public readonly int|null $currentPage = null,
        public readonly int|null $pageCount = null,
    ) {}

    public function count(): int
    {
        return count($this->entities);
    }
}
