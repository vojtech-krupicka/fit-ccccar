<?php declare(strict_types=1);

namespace App\Model;

use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\Database\Explorer;

/**
 * @template T of BaseEntity
 */
abstract class BaseRepository
{
    /**
     * @param BaseMapper<T> $mapper
     */
    public function __construct(
        protected readonly Explorer $dbconn,
        protected readonly BaseMapper $mapper
    ) {}

    public function table(): string
    {
        return $this->mapper->table();
    }

    public function count(): int
    {
        return $this->dbconn->table($this->table())->count();
    }

    /**
     * @return T|null
     */
    public function get(int $id): ?BaseEntity
    {
        $row = $this->dbconn->table($this->table())->get($id);
        return ($row !== null) ? $this->mapper->load($row) : null;
    }

    /**
     * @return EntityList<T> Entity list of all entities of type T.
     */
    public function getAll(?PageRequest $page = null, string $orderBy = ''): EntityList
    {
        $query = $this->dbconn->table($this->table());
        $query->order($orderBy !== '' ? $orderBy : implode(', ', (array) $query->getPrimary()));

        return $this->fetchList($query, $page);
    }

    /**
     * @param T $entity
     */
    public function insert(BaseEntity $entity): void
    {
        $row = $this->dbconn->table($this->table())->insert($this->mapper->dump($entity));
        $entity->id = (int) $row['id'];
    }

    /**
     * @param T $entity
     */
    public function update(BaseEntity $entity): void
    {
        $row = $this->dbconn->table($this->table())->get($entity->id);
        if ($row != null) {
            $row->update($this->mapper->dump($entity));
        }
    }

    public function delete(int $id): bool
    {
        $row = $this->dbconn->table($this->table())->get($id);
        if ($row != null) {
            return $row->delete() > 0;
        }
        return false;
    }

    /**
     * @param Selection<ActiveRow> $query The database query to fetch the list from.
     * @param PageRequest|null $page The pagination information, or null for no pagination.
     * @return EntityList<T>
     */
    protected function fetchList(Selection $query, ?PageRequest $page = null): EntityList
    {
        $count = null;
        $pageCount = 1;
        if ($page !== null) {
            $count = $query->count();
            $query->page($page->page, $page->itemsPerPage, $pageCount);
        }

        $entities = $this->mapper->loadMany($query->fetchAll());
        return new EntityList(
            entities: $entities,
            totalCount: $count ?? count($entities),
            currentPage: $page !== null ? $page->page : 1,
            pageCount: $pageCount,
        );
    }
}
