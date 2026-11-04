<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Datagrids\Datagrid\Types;

use App\Model\BaseEntity;

/**
 * @template T of BaseEntity
 */
trait LinkArgsTrait
{
    /**
     * @var array<string, mixed>|(\Closure(T): array<string, mixed>)
     */
    public protected(set) array|\Closure|null $args = null;

    /**
     * @param T $entity
     * @return array<string, mixed>
     */
    protected function buildArgs(BaseEntity $entity): array
    {
        $args = [];
        if ($this->args instanceof \Closure) {
            $args = ($this->args)($entity);
        } elseif ($this->args === null) {
            $args = ['id' => $entity->id];
        } else {
            $args = $this->args;
        }
        return $args;
    }
}
