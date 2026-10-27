<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Datagrids\Datagrid\Columns;

use App\Model\BaseEntity;
use Nette\Utils\Html;

/**
 * @template T of BaseEntity
 * @extends StringColumn<T>
 */
class TextColumn extends StringColumn
{
    /**
     * @param T $entity
     */
    public function getCellContent(BaseEntity $entity): Html|string|null
    {
        $value = $this->getFieldValue($entity);

        return match (true) {
            $value instanceof Html, is_string($value) => $value,
            is_scalar($value) => (string) $value,
            default => null,
        };
    }
}
