<?php declare(strict_types=1);

namespace App\Model\CarRental;

use App\Model\EntityList;

class CarRentalFacade
{
    public function __construct(
        private CarRentalRepository $carRentalRepository,
    ) {}

    public function getCarRental(int $id): CarRentalEntity|null
    {
        return $this->carRentalRepository->get($id);
    }

    /**
     * @return list<CarRentalEntity>
     */
    public function getCarRentalList(): array
    {
        return $this->carRentalRepository->getAll();
    }

    /**
     * @return EntityList<CarRentalEntity>
     */
    public function getCarRentalListByVehicleId(int $vehicleId): EntityList
    {
        return $this->carRentalRepository->getByVehicleId($vehicleId);
    }

    public function persist(CarRentalEntity $carRental): void
    {
        if ($carRental->id === null) {
            $this->carRentalRepository->insert($carRental);
        } else {
            $this->carRentalRepository->update($carRental);
        }
    }
}
