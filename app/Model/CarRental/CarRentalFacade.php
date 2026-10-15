<?php declare(strict_types=1);

namespace App\Model\CarRental;

use App\Model\BaseFacade;
use App\Model\EntityList;

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
     */
    public function getByVehicleId(int $vehicleId): EntityList
    {
        return $this->repository->getByVehicleId($vehicleId);
    }
}
