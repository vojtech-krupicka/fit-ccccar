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
     * @var array<string, mixed>
     */
    public private(set) array $args;

    protected function renderCellContent(BaseEntity $entity): Html|null
    {
        $value = $this->getFieldValue($entity);
        $link = $this->datagrid->getPresenter()->link($this->target, $this->args);
        return is_string($value) ? Html::el('a')->href($link)->setText($value) : null;
    }

    public function setTarget(string $target): static
    {
        $this->target = $target;
        return $this;
    }

    /**
     * @param array<string, mixed> $args
     */
    public function setArgs(array $args): static
    {
        $this->args = $args;
        return $this;
    }
}
