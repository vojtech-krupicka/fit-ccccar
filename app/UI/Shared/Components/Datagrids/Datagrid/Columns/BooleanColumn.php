<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Datagrids\Datagrid\Columns;

use App\Model\BaseEntity;
use Nette\Utils\Html;

/**
 * @template T of BaseEntity
 * @extends BaseColumn<T>
 */
class BooleanColumn extends BaseColumn
{
    public protected(set) Html|string $trueValue = 'true';
    public protected(set) Html|string $falseValue = 'false';

    protected function renderCellContent(BaseEntity $entity): string
    {
        $value = $this->getFieldValue($entity);
        return ($value === true) ? $this->formatValue($this->trueValue) : $this->formatValue($this->falseValue);
    }

    protected function formatValue(Html|string $value): string
    {
        if ($value instanceof Html) {
            return $value->toHtml();
        } else {
            return (string) $value;
        }
    }

    public function setTrueValue(Html|string $trueValue): static
    {
        $this->trueValue = $trueValue;
        return $this;
    }

    public function setFalseValue(Html|string $falseValue): static
    {
        $this->falseValue = $falseValue;
        return $this;
    }
}
