<?php declare(strict_types=1);

namespace App\UI\Front\Components\Forms\Reservation;

use App\Model\RentalOffice\RentalOfficeFacade;
use App\UI\Shared\Components\Forms\BootstrapFormFactory;
use Nette\Application\UI;

class ReservationFormFactory
{
    public function __construct(
        private readonly BootstrapFormFactory $formFactory,
        private readonly RentalOfficeFacade $rentalOfficeFacade,
    ) {}

    public function create(): UI\Form
    {
        $form = $this->formFactory->create();
        $form
            ->addSelect('rentalOffice', 'Pobočka:')
            ->setPrompt('--- Vyberte pobočku ---')
            ->setRequired('Vyberte pobočku')
            ->setItems(
                array_column($this->rentalOfficeFacade->getAll()->toArray(), 'name', 'id')
            );

        $form
            ->addDate('reserveFrom', 'Od:')
            ->setRequired('Zadejte datum rezervace od:');

        $form
            ->addDate('reserveTo', 'Do:')
            ->setRequired('Zadejte datum rezervace do:');

        $form->addSubmit('reserve', 'Závazně rezervovat')->setOption('icon', 'icon-check');

        return $form;
    }
}
