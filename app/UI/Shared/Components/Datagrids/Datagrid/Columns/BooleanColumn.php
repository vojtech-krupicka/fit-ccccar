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

    public function getCellContent(BaseEntity $entity): Html|string
    {
        $value = $this->getFieldValue($entity);
        return ($value === true) ? $this->trueValue : $this->falseValue;
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
