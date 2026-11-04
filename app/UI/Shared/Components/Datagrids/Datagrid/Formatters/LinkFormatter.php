<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Datagrids\Datagrid\Formatters;

use App\Model\BaseEntity;
use App\UI\Shared\Components\Datagrids\Datagrid\Columns\BaseColumn;
use App\UI\Shared\Components\Datagrids\Datagrid\Types\LinkArgsTrait;
use Nette\Utils\Html;

/**
 * @template T of BaseEntity
 * @extends BaseFormatter<T>
 */
class LinkFormatter extends BaseFormatter
{
    /** @use LinkArgsTrait<T> */
    use LinkArgsTrait;

    /**
     * @param BaseColumn<T> $column
     * @param (\Closure(T): array<string, mixed>)|array<string, mixed>|null $args
     */
    public function __construct(
        BaseColumn $column,
        public readonly string $target,
        \Closure|array|null $args,
    ) {
        parent::__construct($column, '');
        $this->args = $args;
    }

    /**
     * @param T $entity
     */
    public function format(BaseEntity $entity, Html|string $value): Html|string
    {
        $args = $this->buildArgs($entity);
        $link = $this->column->datagrid->getPresenter()->link($this->target, $args);

        $el = Html::el('a')->href($link);
        return $this->setContent($value, $el);
    }
}
