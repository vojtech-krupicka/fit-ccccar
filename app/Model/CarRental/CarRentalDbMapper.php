<?php declare(strict_types=1);

namespace App\Model\CarRental;

use App\Model\BaseEntity;
use App\Model\BaseMapper;
use Nette\Database\Table\ActiveRow;

/**
 * @extends BaseMapper<CarRentalEntity>
 */
class CarRentalDbMapper extends BaseMapper
{
    public function load(?ActiveRow $data): ?CarRentalEntity
    {
        return $data ? new CarRentalEntity(
            id: (int) $data['id'],
            vehicleId: (int) $data['vehicle_id'],
            rentalOfficeId: (int) $data['rental_office_id'],
            customerId: (int) $data['customer_id'],
            borrowerId: isset($data['borrower_id']) ? (int) $data['borrower_id'] : null,
            recipientId: isset($data['recipient_id']) ? (int) $data['recipient_id'] : null,
            reservedAt: $data['reserved_at'],
            reservedFrom: $data['reserved_from'],
            reservedTo: $data['reserved_to'],
            startAt: isset($data['start_at']) ? $data['start_at'] : null,
            endAt: isset($data['end_at']) ? $data['end_at'] : null,
            distance: (float) $data['distance'],
            price: (float) $data['price'],
            deleted: (bool) $data['deleted'],
        ) : null;
    }

    /**
     * Dumps the given car rental entity into an array representation.
     *
     * @param CarRentalEntity $entity The entity to dump.
     * @return array{id: int|null, vehicle_id: int, rental_office_id: int, customer_id: int, borrower_id: int|null,
     *      recipient_id: int|null, reserved_at: string, reserved_from: string, reserved_to: string,
     *      start_at: string|null, end_at: string|null, distance: float, price: float, deleted: bool}
     *      The array representation of the entity.
     */
    public function dump(BaseEntity $entity): array
    {
        return [
            'id' => $entity->id,
            'vehicle_id' => $entity->vehicleId,
            'rental_office_id' => $entity->rentalOfficeId,
            'customer_id' => $entity->customerId,
            'borrower_id' => $entity->borrowerId,
            'recipient_id' => $entity->recipientId,
            'reserved_at' => $entity->reservedAt->format('Y-m-d H:i:s'),
            'reserved_from' => $entity->reservedFrom->format('Y-m-d H:i:s'),
            'reserved_to' => $entity->reservedTo->format('Y-m-d H:i:s'),
            'start_at' => $entity->startAt?->format('Y-m-d H:i:s'),
            'end_at' => $entity->endAt?->format('Y-m-d H:i:s'),
            'distance' => $entity->distance,
            'price' => $entity->price,
            'deleted' => $entity->deleted,
        ];
    }
}
