<?php declare(strict_types=1);

namespace App\Model\RentalOffice;

use App\Model\BaseFacade;
use App\Model\EntityList;
use App\Model\PageRequest;

/**
 * @extends BaseFacade<RentalOfficeEntity, RentalOfficeRepository>
 */
class RentalOfficeFacade extends BaseFacade
{
    public function __construct(
        RentalOfficeRepository $repository,
    ) {
        parent::__construct($repository);
    }

    /**
     * @return EntityList<RentalOfficeEntity>
     */
    public function find(
        RentalOfficeFilter $filter,
        ?PageRequest $page,
    ): EntityList {
        return $this->repository->find($filter, $page);
    }
}
