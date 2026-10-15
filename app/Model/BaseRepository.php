<?php declare(strict_types=1);

namespace App\Model;

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
     * @return list<T> Array of all entities of type T.
     */
    public function getAll(): array
    {
        $rows = $this->dbconn->table($this->table())->fetchAll();
        return $this->mapper->loadMany($rows);
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
}
