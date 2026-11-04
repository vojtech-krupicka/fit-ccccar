<?php declare(strict_types=1);

namespace App\Model\Vehicle;

use App\Model\RentalOffice\RentalOfficeDbMapper;
use App\Model\Vehicle\Color\VehicleColorDbMapper;
use App\Model\Vehicle\FuelType\VehicleFuelTypeDbMapper;
use App\Model\Vehicle\Type\VehicleTypeDbMapper;
use App\Model\BaseEntity;
use App\Model\BaseMapper;
use Nette\Database\Table\ActiveRow;

/**
 * @extends BaseMapper<VehicleEntity>
 */
final class VehicleDbMapper extends BaseMapper
{
    public function __construct(
        private VehicleFuelTypeDbMapper $fuelTypeDbMapper,
        private VehicleColorDbMapper $colorDbMapper,
        private VehicleTypeDbMapper $typeDbMapper,
        private RentalOfficeDbMapper $rentalOfficeDbMapper,
    ) {}

    public function table(): string
    {
        return 'vehicle';
    }

    public function load(?ActiveRow $data): ?VehicleEntity
    {
        return $data ? new VehicleEntity(
            id: (int) $data['id'],
            rentalOfficeId: (int) $data['rental_office_id'],
            typeId: (int) $data['vehicle_type_id'],
            fuelTypeId: (int) $data['vehicle_fuel_type_id'],
            colorId: (int) $data['vehicle_color_id'],
            model: (string) $data['model'],
            plateNumber: (string) $data['plate_number'],
            mileage: (float) $data['mileage'],
            year: (int) $data['year'],
            costPerDay: (float) $data['cost_per_day'],
            costPerUnit: (float) $data['cost_per_unit'],
            fuelType: $this->fuelTypeDbMapper->load($data['fuel_type']),
            color: $this->colorDbMapper->load($data['color']),
            type: $this->typeDbMapper->load($data['type']),
            rentalOffice: $this->rentalOfficeDbMapper->load($data['rental_office']),
        ) : null;
    }

    /**
     * Dumps the given vehicle entity into an array representation.
     *
     * @param VehicleEntity $entity The entity to dump.
     * @return array{id: int|null, rental_office_id: int|null, vehicle_type_id: int|null, vehicle_fuel_type_id: int|null,
     *      vehicle_color_id: int|null, model: string, plate_number: string, mileage: float, year: int,
     *      cost_per_day: float, cost_per_unit: float}
     *      The array representation of the entity.
     */
    public function dump(BaseEntity $entity): array
    {
        return [
            'id' => $entity->id,
            'rental_office_id' => $entity->rentalOfficeId,
            'vehicle_type_id' => $entity->typeId,
            'vehicle_fuel_type_id' => $entity->fuelTypeId,
            'vehicle_color_id' => $entity->colorId,
            'model' => $entity->model,
            'plate_number' => $entity->plateNumber,
            'mileage' => $entity->mileage,
            'year' => $entity->year,
            'cost_per_day' => $entity->costPerDay,
            'cost_per_unit' => $entity->costPerUnit,
        ];
    }
}
