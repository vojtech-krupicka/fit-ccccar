<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Datagrids\Datagrid\Columns;

use App\Model\BaseEntity;
use Nette\Utils\Html;

/**
 * @template T of BaseEntity
 * @extends StringColumn<T>
 */
class LinkColumn extends StringColumn
{
    public private(set) string $target;

    /**
     * @var array<string, mixed>|(\Closure(T): array<string, mixed>)
     */
    public private(set) array|\Closure $args = [];

    public function getCellContent(BaseEntity $entity): Html|null
    {
        $value = $this->getFieldValue($entity);
        $args = $this->args instanceof \Closure ? ($this->args)($entity) : $this->args;
        $link = $this->datagrid->getPresenter()->link($this->target, $args);
        return is_string($value) ? Html::el('a')->href($link)->setText($value) : null;
    }

    public function setTarget(string $target): static
    {
        $this->target = $target;
        return $this;
    }

    /**
     * @param array<string, mixed>|(\Closure(T): array<string, mixed>) $args
     */
    public function setArgs(array|\Closure $args): static
    {
        $this->args = $args;
        return $this;
    }
}
