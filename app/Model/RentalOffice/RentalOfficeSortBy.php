<?php declare(strict_types=1);

namespace App\Model\RentalOffice;

enum RentalOfficeSortBy: string
{
    case Id = 'id';
    case Name = 'rental_office_name';
}
