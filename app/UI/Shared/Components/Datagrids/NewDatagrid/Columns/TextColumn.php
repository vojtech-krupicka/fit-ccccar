<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Datagrids\NewDatagrid\Columns;

use App\Model\BaseEntity;
use Nette\Utils\Html;

/**
 * @template T of BaseEntity
 * @extends BaseColumn<T>
 */
class TextColumn extends BaseColumn
{
    public function getCellValue(BaseEntity $entity): string|null
    {
        $value = $this->getEntityField($entity, $this->field);
        return is_string($value) ? $value : null;
    }
}
