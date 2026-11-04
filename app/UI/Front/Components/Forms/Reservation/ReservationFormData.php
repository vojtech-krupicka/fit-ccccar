<?php declare(strict_types=1);

namespace App\UI\Front\Components\Forms\Reservation;

class ReservationFormData
{
    public readonly \DateTime $reserveFrom;
    public readonly \DateTime $reserveTo;

    public function __construct(
        public readonly int $rentalOffice,
        \DateTimeInterface $reserveFrom,
        \DateTimeInterface $reserveTo,
    ) {
        // the date inputs hand over immutable dates; the entity works with mutable ones
        $this->reserveFrom = \DateTime::createFromInterface($reserveFrom)->setTime(0, 0, 0);
        $this->reserveTo = \DateTime::createFromInterface($reserveTo)->setTime(23, 59, 59);
    }
}
