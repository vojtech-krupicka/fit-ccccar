<?php declare(strict_types=1);

namespace App\Model;

/**
 * @template T of BaseEntity
 * @template R of BaseRepository<T>
 */
abstract class BaseFacade
{
    /**
     * @param R $repository
     */
    public function __construct(
        protected readonly BaseRepository $repository
    ) {}

    /**
     * @return T|null
     */
    public function get(int $id): ?BaseEntity
    {
        return $this->repository->get($id);
    }

    /**
     * @return EntityList<T> Entity list of all entities of type T.
     */
    public function getAll(?PageRequest $page = null): EntityList
    {
        return $this->repository->getAll($page);
    }

    /**
     * @return int The count of all entities of type T (no filters applied).
     */
    public function getCount(): int
    {
        return $this->repository->count();
    }

    /**
     * @param T $entity
     */
    public function persist(BaseEntity $entity): bool
    {
        try {
            if ($entity->id === null) {
                $this->repository->insert($entity);
            } else {
                $this->repository->update($entity);
            }
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * @param T $entity
     */
    public function delete(BaseEntity $entity): bool
    {
        return ($entity->id > 0) ? $this->repository->delete($entity->id) : false;
    }
}
