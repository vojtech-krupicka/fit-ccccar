<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Datagrids\NewDatagrid\Formatters;

use App\Model\BaseEntity;
use App\UI\Shared\Components\Datagrids\NewDatagrid\Columns\BaseColumn;
use App\UI\Shared\Components\Datagrids\NewDatagrid\Types\GetEntityFieldTrait;
use Nette\Utils\Html;

/**
 * @template T of BaseEntity
 */
abstract class BaseFormatter
{
    use GetEntityFieldTrait;

    /**
     * @param BaseColumn<T> $column
     * @param (\Closure(T): string|null)|string $field
     */
    public function __construct(
        public readonly BaseColumn $column,
        \Closure|string $field
    ) {
        $this->field = $field;
    }

    /**
     * @param T $entity
     */
    public abstract function format(BaseEntity $entity, Html|string $value): Html|string;

    protected function setContent(Html|string $value, Html $el): Html
    {
        if ($value instanceof Html) {
            $el->setHtml($value);
        } else {
            $el->setText($value);
        }
        return $el;
    }

    protected function addContent(Html|string $value, Html $el): Html
    {
        if ($value instanceof Html) {
            $el->addHtml($value);
        } else {
            $el->addText($value);
        }
        return $el;
    }
}
