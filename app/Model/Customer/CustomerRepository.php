<?php declare(strict_types=1);

namespace App\Model\Customer;

use App\Model\BaseRepository;

class CustomerRepository extends BaseRepository
{
    public function __construct(
        private readonly \Nette\Database\Explorer $dbconn,
        private readonly CustomerDbMapper $customerDbMapper,
    ) {}

    public function get(int $id): CustomerEntity|null
    {
        $row = $this->dbconn->table('customer')->get($id);
        return $this->customerDbMapper->load($row);
    }

    public function getByEmail(string $email): CustomerEntity|null
    {
        $row = $this->dbconn->table('customer')->where('email', $email)->fetch();
        return $this->customerDbMapper->load($row);
    }

    public function insert(CustomerEntity $customer): void
    {
        $row = $this->dbconn->table('customer')->insert($this->customerDbMapper->dump($customer));
        $customer->id = (int) $row['id'];
    }

    public function update(CustomerEntity $customer): void
    {
        $row = $this->dbconn->table('customer')->where('id', $customer->id);
        if ($row != null) {
            $row->update($this->customerDbMapper->dump($customer));
        }
    }
}
