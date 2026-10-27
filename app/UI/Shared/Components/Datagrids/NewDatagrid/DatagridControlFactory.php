<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Datagrids\NewDatagrid;

use App\Model\BaseEntity;
use App\Model\EntityList;

interface DatagridControlFactory
{
    /**
     * @template T of BaseEntity
     * @param EntityList<T> $entityList
     * @return DatagridControl<T>
     */
    public function create(EntityList $entityList): DatagridControl;
}
