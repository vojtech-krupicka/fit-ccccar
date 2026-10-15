<?php declare(strict_types=1);

namespace App\Model\Vehicle\Type;

use App\Model\BaseEntity;
use App\Model\BaseMapper;
use Nette\Database\Table\ActiveRow;

/**
 * @extends BaseMapper<VehicleTypeEntity>
 */
final class VehicleTypeDbMapper extends BaseMapper
{
    public function load(?ActiveRow $data): ?VehicleTypeEntity
    {
        return $data ? new VehicleTypeEntity(
            id: (int) $data['id'],
            manufacturer: (string) $data['manufacturer'],
            brand: (string) $data['brand'],
        ) : null;
    }

    /**
     * Dumps the given vehicle entity into an array representation.
     *
     * @param VehicleTypeEntity $entity The entity to dump.
     * @return array{id: int|null, manufacturer: string, brand: string}
     *      The array representation of the entity.
     */
    public function dump(BaseEntity $entity): array
    {
        return [
            'id' => $entity->id,
            'manufacturer' => $entity->manufacturer,
            'brand' => $entity->brand,
        ];
    }
}
