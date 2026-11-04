<?php declare(strict_types=1);

namespace App\Model\CarRental;

enum CarRentalSortBy: string
{
    case Id = 'id';
    case Vehicle = 'vehicle_id';
    case Customer = 'customer_id';
    case RentalOffice = 'rental_office_id';
    case Borrower = 'borrower_id';
    case Recipient = 'recipient_id';
    case Latest = 'latest';  // newest reservations first
    case Oldest = 'oldest';
    case Distance = 'distance';
    case Price = 'price';
    case ReservedAt = 'reserved_at';
    case ReservedFrom = 'reserved_from';
    case StartAt = 'start_at';
    case Deleted = 'deleted';
}
