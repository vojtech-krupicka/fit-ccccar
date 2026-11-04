<?php declare(strict_types=1);

namespace App\Model\Employee;

use App\Model\BaseEntity;
use App\Model\BaseMapper;
use Nette\Database\Table\ActiveRow;

/**
 * @extends BaseMapper<EmployeeEntity>
 */
class EmployeeDbMapper extends BaseMapper
{
    public function table(): string
    {
        return 'employee';
    }

    public function load(?ActiveRow $data): ?EmployeeEntity
    {
        return $data ? new EmployeeEntity(
            id: (int) $data['id'],
            rentalOfficeId: (int) $data['rental_office_id'],
            password: (string) $data['password'],
            email: (string) $data['email'],
            firstname: (string) $data['name'],
            surname: (string) $data['surname'],
            phone: (string) $data['phone'],
        ) : null;
    }

    /**
     * Dumps the given employee entity into an array representation.
     *
     * @param EmployeeEntity $entity The entity to dump.
     * @return array{password: string, email: string, name: string, surname: string,
     *      rental_office_id: int|null, phone: string} but returns array{rental_office_id: int|null,
     *      password: string, email: string, name: string, surname: string, phone: string}
     *      The array representation of the employee entity.
     */
    public function dump(BaseEntity $entity): array
    {
        return [
            'rental_office_id' => $entity->rentalOfficeId,
            'password' => $entity->password,
            'email' => $entity->email,
            'name' => $entity->firstname,
            'surname' => $entity->surname,
            'phone' => $entity->phone,
        ];
    }
}
