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
use App\Model\EntityList;
use App\Model\SortMode;
use App\UI\Front\Components\Forms\Catalog\SortBy;

class VehicleFacade
{
    public function __construct(
        private VehicleColorRepository $vehicleColorRepository,
        private VehicleFuelTypeRepository $vehicleFuelTypeRepository,
        private VehicleTypeRepository $vehicleTypeRepository,
        private VehicleRepository $vehicleRepository,
    ) {}

    public function getVehicle(int $id): VehicleEntity|null
    {
        return $this->vehicleRepository->get($id);
    }

    public function getVehicleByVin(string $vin): VehicleEntity|null
    {
        return null;  // $this->vehicleRepository->getByVin($vin);
    }

    /**
     * @return list<VehicleColorEntity>
     */
    public function getVehicleColorList(): array
    {
        return $this->vehicleColorRepository->getAll();
    }

    /**
     * @return list<VehicleFuelTypeEntity>
     */
    public function getVehicleFuelTypeList(): array
    {
        return $this->vehicleFuelTypeRepository->getAll();
    }

    /**
     * @return list<VehicleTypeEntity>
     */
    public function getVehicleTypeList(): array
    {
        return $this->vehicleTypeRepository->getAll();
    }

    /**
     * @return int
     */
    public function getVehicleCount(): int
    {
        return $this->vehicleRepository->getCount();
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
        return $this->vehicleRepository->find(
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
