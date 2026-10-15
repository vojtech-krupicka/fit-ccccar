<?php declare(strict_types=1);

namespace App\Model\RentalOffice;

use App\Model\BaseRepository;

class RentalOfficeRepository extends BaseRepository
{
    public function __construct(
        private readonly \Nette\Database\Explorer $dbconn,
        private readonly RentalOfficeDbMapper $rentalOfficeDbMapper,
    ) {}

    public function get(int $id): RentalOfficeEntity|null
    {
        $row = $this->dbconn->table('rental_office')->get($id);
        return $this->rentalOfficeDbMapper->load($row);
    }

    public function insert(RentalOfficeEntity $rentalOffice): void
    {
        $row = $this->dbconn->table('rental_office')->insert($this->rentalOfficeDbMapper->dump($rentalOffice));
        $rentalOffice->id = (int) $row['id'];
    }

    public function update(RentalOfficeEntity $rentalOffice): void
    {
        $row = $this->dbconn->table('rental_office')->get($rentalOffice->id);
        if ($row != null) {
            $row->update($this->rentalOfficeDbMapper->dump($rentalOffice));
        }
    }

    public function delete(int $id): void
    {
        $row = $this->dbconn->table('rental_office')->get($id);
        if ($row != null) {
            $row->delete();
        }
    }

    /**
     * @return list<RentalOfficeEntity> Array of all rental office entities.
     */
    public function getAll(): array
    {
        $rows = $this->dbconn->table('rental_office')->fetchAll();
        return $this->rentalOfficeDbMapper->loadMany($rows);
    }
}
