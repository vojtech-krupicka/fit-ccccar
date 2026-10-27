<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Datagrids\NewDatagrid\Columns;

use App\Model\BaseEntity;
use Nette\Utils\Html;

/**
 * @template T of BaseEntity
 * @extends BaseColumn<T>
 */
class CallableColumn extends BaseColumn
{
    /** @var (\Closure(T): Html|string)|null */
    public protected(set) ?\Closure $callback = null;

    public function getCellValue(BaseEntity $entity): Html|string|null
    {
        return $this->callback instanceof \Closure ? ($this->callback)($entity) : null;
    }

    /**
     * @param (\Closure(T): Html|string)|null $callback
     */
    public function setCallback(\Closure $callback): static
    {
        $this->callback = $callback;
        return $this;
    }
}
