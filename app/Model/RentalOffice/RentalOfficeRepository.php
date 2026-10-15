<?php declare(strict_types=1);

namespace App\Model\RentalOffice;

use App\Model\BaseRepository;
use Nette\Database\Explorer;

/**
 * @extends BaseRepository<RentalOfficeEntity>
 */
class RentalOfficeRepository extends BaseRepository
{
    public function __construct(Explorer $dbconn, RentalOfficeDbMapper $mapper)
    {
        parent::__construct($dbconn, $mapper);
    }
}
