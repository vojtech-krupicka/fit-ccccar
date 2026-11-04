<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Datagrids\Datagrid\Types;

use App\Model\BaseEntity;

/**
 * @template T of BaseEntity
 */
trait GetEntityFieldTrait
{
    /**
     * @var (\Closure(T): (string|null))|string $field
     */
    protected \Closure|string $field;

    /**
     * @param T $entity
     * @param (\Closure(T): (string|null))|string $field
     */
    protected function getEntityField(BaseEntity $entity, \Closure|string $field): mixed
    {
        if ($field instanceof \Closure) {
            return $field($entity);
        }

        $parts = explode('.', $field);
        $current = $entity;
        foreach ($parts as $part) {
            if ($current === null) {
                return null;
            }
            $current = $current->{$part} ?? null;
        }

        return $current;
    }
}
