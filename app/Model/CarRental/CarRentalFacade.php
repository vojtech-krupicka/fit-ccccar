<?php declare(strict_types=1);

namespace App\Model\CarRental;

use App\Model\BaseFacade;
use App\Model\EntityList;
use App\Model\PageRequest;

/**
 * @extends BaseFacade<CarRentalEntity, CarRentalRepository>
 */
class CarRentalFacade extends BaseFacade
{
    public function __construct(CarRentalRepository $repository)
    {
        parent::__construct($repository);
    }

    /**
     * @return EntityList<CarRentalEntity>
     * @deprecated Use the find method with CarRentalFilter instead.
     */
    public function getByVehicleId(int $vehicleId): EntityList
    {
        return $this->repository->getByVehicleId($vehicleId);
    }

    /**
     * @return EntityList<CarRentalEntity>
     */
    public function find(CarRentalFilter $filter, ?PageRequest $page = null): EntityList
    {
        return $this->repository->find($filter, $page);
    }
}
