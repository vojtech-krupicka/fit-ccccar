<?php declare(strict_types=1);

namespace App\Model\RentalOffice;

use App\Model\Employee\EmployeeDbMapper;
use App\Model\BaseEntity;
use App\Model\BaseMapper;
use App\Model\EntityList;
use Nette\Database\Table\ActiveRow;

/**
 * @extends BaseMapper<RentalOfficeEntity>
 */
class RentalOfficeDbMapper extends BaseMapper
{
    public function __construct(
        private EmployeeDbMapper $employeeDbMapper,
    ) {}

    public function table(): string
    {
        return 'rental_office';
    }

    public function load(?ActiveRow $data): ?RentalOfficeEntity
    {
        $entities = $this->employeeDbMapper->loadMany(
            $data->related('employee.rental_office_id')->fetchAll()
        );
        return $data ? new RentalOfficeEntity(
            id: (int) $data['id'],
            supervisorId: (int) $data['supervisor_id'],
            name: (string) $data['name'],
            street: (string) $data['street'],
            city: (string) $data['city'],
            zip: (string) $data['zip'],
            country: (string) $data['country'],
            phone: (string) $data['phone'],
            email: (string) $data['email'],
            latitude: (float) $data['lat'],
            longitude: (float) $data['lon'],
            supervisor: $this->employeeDbMapper->load($data['supervisor']),
            employeeList: new EntityList(
                entities: $entities,
                totalCount: count($entities)
            )
        ) : null;
    }

    /**
     * Dumps the given rental office entity into an array representation.
     *
     * @param RentalOfficeEntity $entity The entity to dump.
     * @return array{id: int|null, supervisor_id: int|null, name: string, street: string, city: string, zip: string,
     *      country: string, phone: string, email: string, lat: float, lon: float}
     *      The array representation of the entity.
     */
    public function dump(BaseEntity $entity): array
    {
        return [
            'id' => $entity->id,
            'supervisor_id' => $entity->supervisorId,
            'name' => $entity->name,
            'street' => $entity->street,
            'city' => $entity->city,
            'zip' => $entity->zip,
            'country' => $entity->country,
            'phone' => $entity->phone,
            'email' => $entity->email,
            'lat' => $entity->latitude,
            'lon' => $entity->longitude,
        ];
    }
}
