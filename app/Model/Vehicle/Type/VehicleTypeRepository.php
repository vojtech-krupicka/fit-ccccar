<?php declare(strict_types=1);

namespace App\Model\Vehicle\Type;

use App\Model\BaseRepository;
use Nette\Database\Explorer;

/**
 * @extends BaseRepository<VehicleTypeEntity>
 */
class VehicleTypeRepository extends BaseRepository
{
    public function __construct(Explorer $dbconn, VehicleTypeDbMapper $mapper)
    {
        parent::__construct($dbconn, $mapper);
    }
}
