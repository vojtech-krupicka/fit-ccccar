<?php declare(strict_types=1);

namespace App\UI\Front\Components\Forms\Reservation;

class ReservationFormData
{
    public readonly \DateTime $reserveFrom;
    public readonly \DateTime $reserveTo;

    public function __construct(
        public readonly int $rentalOffice,
        \DateTime $reserveFrom,
        \DateTime $reserveTo,
    ) {
        $this->reserveFrom = $reserveFrom->setTime(0, 0, 0);
        $this->reserveTo = $reserveTo->setTime(23, 59, 59);
    }
}
