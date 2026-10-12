<?php declare(strict_types=1);

namespace App\Model\Customer;

use App\Model\BaseRepository;

class CustomerRepository extends BaseRepository
{
    public function get(int $id): CustomerEntity|null
    {
        $row = $this->dbconn->table('customer')->get($id);
        return CustomerDbMapper::load($row);
    }

    public function getByEmail(string $email): CustomerEntity|null
    {
        $row = $this->dbconn->table('customer')->where('email', $email)->fetch();
        return CustomerDbMapper::load($row);
    }
}
