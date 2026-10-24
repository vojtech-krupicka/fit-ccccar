<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Datagrids\Datagrid\Columns;

use App\Model\BaseEntity;
use Nette\Utils\Html;

/**
 * @template T of BaseEntity
 * @extends StringColumn<T>
 */
class UrlColumn extends StringColumn
{
    protected function renderCellContent(BaseEntity $entity): Html|null
    {
        $value = $this->getFieldValue($entity);
        return is_string($value) ? Html::el('a')->href($value)->setText($value) : null;
    }
}
