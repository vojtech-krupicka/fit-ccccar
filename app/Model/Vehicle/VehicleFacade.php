<?php declare(strict_types=1);

namespace App\Model\Vehicle;

use App\Model\Vehicle\Color\VehicleColorEntity;
use App\Model\Vehicle\Color\VehicleColorRepository;
use App\Model\Vehicle\FuelType\VehicleFuelTypeEntity;
use App\Model\Vehicle\FuelType\VehicleFuelTypeRepository;
use App\Model\Vehicle\Type\VehicleTypeEntity;
use App\Model\Vehicle\Type\VehicleTypeRepository;
use App\Model\Vehicle\VehicleEntity;
use App\Model\Vehicle\VehicleFilter;
use App\Model\Vehicle\VehicleRepository;
use App\Model\BaseFacade;
use App\Model\EntityList;
use App\Model\PageRequest;

/**
 * @extends BaseFacade<VehicleEntity, VehicleRepository>
 */
class VehicleFacade extends BaseFacade
{
    public function __construct(
        private VehicleColorRepository $colorRepository,
        private VehicleFuelTypeRepository $fuelTypeRepository,
        private VehicleTypeRepository $typeRepository,
        VehicleRepository $repository,
    ) {
        parent::__construct($repository);
    }

    public function getByVin(string $vin): VehicleEntity|null
    {
        return null;  // $this->vehicleRepository->getByVin($vin);
    }

    /**
     * @return EntityList<VehicleColorEntity>
     */
    public function getColorList(): EntityList
    {
        return $this->colorRepository->getAll(orderBy: 'name');
    }

    /**
     * @return EntityList<VehicleFuelTypeEntity>
     */
    public function getFuelTypeList(): EntityList
    {
        return $this->fuelTypeRepository->getAll(orderBy: 'fuel');
    }

    /**
     * @return EntityList<VehicleTypeEntity>
     */
    public function getTypeList(): EntityList
    {
        return $this->typeRepository->getAll();
    }

    /**
     * @return EntityList<VehicleEntity>
     */
    public function find(
        VehicleFilter $filter,
        PageRequest $page,
    ): EntityList {
        return $this->repository->find($filter, $page);
    }
}
