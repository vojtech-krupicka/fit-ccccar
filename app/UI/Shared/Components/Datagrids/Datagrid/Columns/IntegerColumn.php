<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Datagrids\Datagrid\Columns;

use App\Model\BaseEntity;

/**
 * @template T of BaseEntity
 * @extends BaseColumn<T>
 */
class IntegerColumn extends BaseColumn
{
    public function getCellContent(BaseEntity $entity): string|null
    {
        $value = $this->getFieldValue($entity);
        return is_int($value) ? (string) $value : null;
    }
}
