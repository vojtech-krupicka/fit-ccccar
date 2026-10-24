<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Datagrids\Datagrid\Columns;

use App\Model\BaseEntity;
use App\UI\Shared\Components\Datagrids\Datagrid\Utils\TextAlign;

/**
 * @template T of BaseEntity
 * @extends BaseColumn<T>
 */
abstract class StringColumn extends BaseColumn
{
    public protected(set) TextAlign $textAlign = TextAlign::Left;
}
