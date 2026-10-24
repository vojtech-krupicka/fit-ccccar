<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Datagrids\Datagrid\Columns;

use App\Model\BaseEntity;
use Nette\Utils\Html;

/**
 * @template T of BaseEntity
 * @extends StringColumn<T>
 */
class TextColumn extends StringColumn
{
    /** @var (\Closure(T): string)|null */
    public protected(set) ?\Closure $callback = null;

    /**
     * @param T $entity
     */
    protected function renderCellContent(BaseEntity $entity): Html|string|null
    {
        if ($this->callback !== null) {
            $content = ($this->callback)($entity);
        } else {
            $content = $this->getFieldValue($entity);
        }

        return $content;
    }

    /**
     * @param (\Closure(T): string)|null $callback
     */
    public function setCallback(?\Closure $callback): static
    {
        $this->callback = $callback;
        return $this;
    }
}
