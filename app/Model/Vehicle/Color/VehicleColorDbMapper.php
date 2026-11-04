<?php declare(strict_types=1);

namespace App\Model\Vehicle\Color;

use App\Model\BaseEntity;
use App\Model\BaseMapper;
use Nette\Database\Table\ActiveRow;

/**
 * @extends BaseMapper<VehicleColorEntity>
 */
final class VehicleColorDbMapper extends BaseMapper
{
    public function table(): string
    {
        return 'vehicle_color';
    }

    public function load(?ActiveRow $data): ?VehicleColorEntity
    {
        return $data !== null ? new VehicleColorEntity(
            id: (int) $data['id'],
            name: (string) $data['name'],
            hex: (string) $data['ean'],
        ) : null;
    }

    /**
     * Dumps the given vehicle entity into an array representation.
     *
     * @param VehicleColorEntity $entity The entity to dump.
     * @return array{id: int|null, name: string, hex: string}
     *      The array representation of the entity.
     */
    public function dump(BaseEntity $entity): array
    {
        return [
            'id' => $entity->id,
            'name' => $entity->name,
            'hex' => $entity->hex,
        ];
    }
}
