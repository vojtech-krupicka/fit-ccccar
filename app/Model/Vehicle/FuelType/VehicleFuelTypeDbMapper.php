<?php declare(strict_types=1);

namespace App\Model\Vehicle\FuelType;

use App\Model\BaseEntity;
use App\Model\BaseMapper;
use Nette\Database\Table\ActiveRow;

/**
 * @extends BaseMapper<VehicleFuelTypeEntity>
 */
final class VehicleFuelTypeDbMapper extends BaseMapper
{
    public function table(): string
    {
        return 'vehicle_fuel_type';
    }

    public function load(?ActiveRow $data): ?VehicleFuelTypeEntity
    {
        return $data !== null ? new VehicleFuelTypeEntity(
            id: (int) $data['id'],
            name: (string) $data['fuel'],
        ) : null;
    }

    /**
     * Dumps the given vehicle fuel typeentity into an array representation.
     *
     * @param VehicleFuelTypeEntity $entity The entity to dump.
     * @return array{id: int|null, name: string}
     *      The array representation of the entity.
     */
    public function dump(BaseEntity $entity): array
    {
        return [
            'id' => $entity->id,
            'name' => $entity->name,
        ];
    }
}
