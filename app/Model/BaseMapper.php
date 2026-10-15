<?php declare(strict_types=1);

namespace App\Model;

use Nette\Database\Table\ActiveRow;

/**
 * @template T of BaseEntity
 */
abstract class BaseMapper
{
    /**
     * Loads an entity from the given data.
     *
     * @param ActiveRow|null $data The data to load the entity from.
     * @return T|null The loaded entity or null if loading failed.
     */
    abstract public function load(?ActiveRow $data): ?BaseEntity;

    /**
     * Dumps the given entity into an array representation.
     *
     * @param T $entity The entity to dump.
     * @return array<string, mixed> The array representation of the entity.
     */
    abstract public function dump(BaseEntity $entity): array;

    /**
     * Loads multiple entities from the given data array.
     *
     * @param iterable<ActiveRow> $data The array of data to load entities from.
     * @return list<T> The list of loaded entities.
     */
    public function loadMany(iterable $data): array
    {
        $entities = [];
        foreach ($data as $item) {
            $entity = $this->load($item);
            if ($entity !== null) {
                $entities[] = $entity;
            }
        }
        return $entities;
    }
}
