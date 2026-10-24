<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Datagrids\Datagrid\Columns;

use App\Model\BaseEntity;

/**
 * @template T of BaseEntity
 * @extends BaseColumn<T>
 */
class DatetimeColumn extends BaseColumn
{
    public protected(set) ?string $format = 'Y-m-d H:i:s';

    protected function renderCellContent(BaseEntity $entity): string|null
    {
        $value = $this->getFieldValue($entity);
        return $value instanceof \DateTimeInterface ? $value->format($this->getFormat()) : null;
    }

    public function setFormat(string $format): static
    {
        $this->format = $format;
        return $this;
    }

    public function getFormat(): string
    {
        return $this->format ?? $this->datagrid->dateFormat;
    }
}
