<?php declare(strict_types=1);

namespace App\Model\Customer;

use App\Model\BaseEntity;
use App\Model\BaseMapper;
use Nette\Database\Table\ActiveRow;

/**
 * @extends BaseMapper<CustomerEntity>
 */
class CustomerDbMapper extends BaseMapper
{
    public function load(?ActiveRow $data): ?CustomerEntity
    {
        return $data ? new CustomerEntity(
            id: (int) $data['id'],
            password: (string) $data['password'],
            email: (string) $data['email'],
            firstname: (string) $data['name'],
            surname: (string) $data['surname'],
            identityNumber: (string) $data['identity_number'],
            phone: (string) $data['phone'],
            street: (string) $data['street'],
            city: (string) $data['city'],
            zip: (string) $data['zip'],
            country: (string) $data['country'],
            created: $data['created'] ?? new \DateTime(),
        ) : null;
    }

    /**
     * Dumps the given customer entity into an array representation.
     *
     * @param CustomerEntity $entity The entity to dump.
     * @return array{password: string, email: string, name: string, surname: string,
     *      identity_number: string, phone: string, street: string, city: string, zip: string,
     *      country: string, created_at: \DateTime|null}
     *      The array representation of the entity.
     */
    public function dump(BaseEntity $entity): array
    {
        return [
            'password' => $entity->password,
            'email' => $entity->email,
            'name' => $entity->firstname,
            'surname' => $entity->surname,
            'identity_number' => $entity->identityNumber,
            'phone' => $entity->phone,
            'street' => $entity->street,
            'city' => $entity->city,
            'zip' => $entity->zip,
            'country' => $entity->country,
            'created_at' => $entity->created,
        ];
    }
}
