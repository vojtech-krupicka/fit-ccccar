<?php declare(strict_types=1);

namespace App\Model\Vehicle\FuelType;

use App\Model\BaseRepository;
use Nette\Database\Explorer;

/**
 * @extends BaseRepository<VehicleFuelTypeEntity>
 */
class VehicleFuelTypeRepository extends BaseRepository
{
    public function __construct(Explorer $dbconn, VehicleFuelTypeDbMapper $mapper)
    {
        parent::__construct($dbconn, $mapper);
    }
}
