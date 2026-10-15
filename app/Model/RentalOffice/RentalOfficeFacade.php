<?php declare(strict_types=1);

namespace App\Model\RentalOffice;

use App\Model\Vehicle\Color\VehicleColorEntity;
use App\Model\Vehicle\Color\VehicleColorRepository;
use App\Model\Vehicle\FuelType\VehicleFuelTypeEntity;
use App\Model\Vehicle\FuelType\VehicleFuelTypeRepository;
use App\Model\Vehicle\Type\VehicleTypeEntity;
use App\Model\Vehicle\Type\VehicleTypeRepository;
use App\Model\Vehicle\VehicleEntity;
use App\Model\Vehicle\VehicleRepository;

class RentalOfficeFacade
{
    public function __construct(
        private RentalOfficeRepository $rentalOfficeRepository,
    ) {}

    public function getRentalOffice(int $id): RentalOfficeEntity|null
    {
        return $this->rentalOfficeRepository->get($id);
    }

    /**
     * @return list<RentalOfficeEntity>
     */
    public function getRentalOfficeList(): array
    {
        return $this->rentalOfficeRepository->getAll();
    }
}
