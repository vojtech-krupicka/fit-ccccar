<?php declare(strict_types=1);

namespace App\Model\RentalOffice;

use App\Model\BaseFacade;

/**
 * @extends BaseFacade<RentalOfficeEntity, RentalOfficeRepository>
 */
class RentalOfficeFacade extends BaseFacade
{
    public function __construct(
        RentalOfficeRepository $repository,
    ) {
        parent::__construct($repository);
    }
}
