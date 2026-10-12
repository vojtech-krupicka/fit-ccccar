<?php declare(strict_types=1);

namespace App\Model;

abstract class BaseMapper
{
    abstract public static function load(mixed $data): BaseEntity;

    /**
     * Dumps the given entity into an array representation.
     *
     * @param BaseEntity $entity The entity to dump.
     * @return array<string, mixed> The array representation of the entity.
     */
    abstract public static function dump(BaseEntity $entity): array;
}
