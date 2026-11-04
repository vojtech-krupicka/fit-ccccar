<?php declare(strict_types=1);

namespace App\Model\Vehicle\Color;

use App\Model\BaseRepository;
use Nette\Database\Explorer;

/**
 * @extends BaseRepository<VehicleColorEntity>
 */
class VehicleColorRepository extends BaseRepository
{
    public function __construct(Explorer $dbconn, VehicleColorDbMapper $mapper)
    {
        parent::__construct($dbconn, $mapper);
    }
}
