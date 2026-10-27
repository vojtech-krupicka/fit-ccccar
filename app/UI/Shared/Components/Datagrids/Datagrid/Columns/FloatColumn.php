<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Datagrids\Datagrid\Columns;

use App\Model\BaseEntity;

/**
 * @template T of BaseEntity
 * @extends BaseColumn<T>
 */
class FloatColumn extends BaseColumn
{
    public protected(set) int $decimals = 2;
    public protected(set) string $decPoint = '.';
    public protected(set) string $thousandsSep = ',';

    public function getCellContent(BaseEntity $entity): string|null
    {
        $value = $this->getFieldValue($entity);
        return is_float($value) ? number_format($value, $this->decimals, $this->decPoint, $this->thousandsSep) : null;
    }

    public function setDecimals(int $decimals): static
    {
        $this->decimals = $decimals;
        return $this;
    }

    public function setDecPoint(string $decPoint): static
    {
        $this->decPoint = $decPoint;
        return $this;
    }

    public function setThousandsSep(string $thousandsSep): static
    {
        $this->thousandsSep = $thousandsSep;
        return $this;
    }
}
