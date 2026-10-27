<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Datagrids\NewDatagrid\Columns;

use App\Model\BaseEntity;
use Nette\Utils\Html;

/**
 * @template T of BaseEntity
 * @extends BaseColumn<T>
 */
class DatetimeColumn extends BaseColumn
{
    public protected(set) ?string $format = null;

    public function getCellValue(BaseEntity $entity): string|null
    {
        $value = $this->getEntityField($entity, $this->field);
        $format = $this->format !== null ? $this->format : $this->datagrid->dateFormat;
        return $value instanceof \DateTimeInterface ? $value->format($format) : null;
    }

    public function setFormat(?string $format): static
    {
        $this->format = $format;
        return $this;
    }
}
