<?php declare(strict_types=1);

namespace App\Model\Vehicle;

use App\Model\Vehicle\Color\VehicleColorEntity;
use App\Model\Vehicle\Color\VehicleColorRepository;
use App\Model\Vehicle\FuelType\VehicleFuelTypeEntity;
use App\Model\Vehicle\FuelType\VehicleFuelTypeRepository;
use App\Model\Vehicle\Type\VehicleTypeEntity;
use App\Model\Vehicle\Type\VehicleTypeRepository;
use App\Model\Vehicle\VehicleAvailability;
use App\Model\Vehicle\VehicleEntity;
use App\Model\Vehicle\VehicleRepository;
use App\Model\BaseFacade;
use App\Model\EntityList;
use App\Model\SortMode;
use App\UI\Front\Components\Forms\Catalog\SortBy;

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
     * @return list<VehicleColorEntity>
     */
    public function getColorList(): array
    {
        return $this->colorRepository->getAll();
    }

    /**
     * @return list<VehicleFuelTypeEntity>
     */
    public function getFuelTypeList(): array
    {
        return $this->fuelTypeRepository->getAll();
    }

    /**
     * @return list<VehicleTypeEntity>
     */
    public function getTypeList(): array
    {
        return $this->typeRepository->getAll();
    }

    /**
     * @return EntityList<VehicleEntity>
     */
    public function getAllVehicles(
        ?int $rentalOffice = null,
        ?int $fuelType = null,
        ?int $color = null,
        ?float $costPerDayFrom = null,
        ?float $costPerDayTo = null,
        ?float $costPerUnitFrom = null,
        ?float $costPerUnitTo = null,
        ?VehicleAvailability $availability = null,
        SortBy $sortBy = SortBy::VehicleName,
        SortMode $sortMode = SortMode::Ascending,
        int $page = 1,
        int $itemsPerPage = 100,
    ): EntityList {
        return $this->repository->find(
            $rentalOffice,
            $fuelType,
            $color,
            $costPerDayFrom,
            $costPerDayTo,
            $costPerUnitFrom,
            $costPerUnitTo,
            $availability,
            $sortBy,
            $sortMode,
            $page,
            $itemsPerPage,
        );
    }
}
