<?php declare(strict_types=1);

namespace App\UI\Front\Components\Forms\Catalog;

use App\Model\RentalOffice\RentalOfficeFacade;
use App\Model\Vehicle\VehicleAvailability;
use App\Model\Vehicle\VehicleFacade;
use App\Model\SortMode;
use App\UI\Shared\Components\Forms\BootstrapFormFactory;
use Nette\Application\UI;

class FilterFormFactory
{
    public function __construct(
        private readonly BootstrapFormFactory $formFactory,
        private readonly RentalOfficeFacade $rentalOfficeFacade,
        private readonly VehicleFacade $vehicleFacade,
    ) {}

    public function create(): UI\Form
    {
        $form = $this->formFactory->create();
        $form
            ->addSelect('rentalOffice', 'Pobočka:')
            ->setPrompt('--- Vyberte pobočku ---')
            ->setItems(
                array_column($this->rentalOfficeFacade->getAll(), 'name', 'id')
            );

        $form
            ->addSelect('availability', 'Dostupnost:')
            ->setPrompt('--- Nezáleží ---')
            ->setItems([
                VehicleAvailability::Available->value => 'Volné',
                VehicleAvailability::Reserved->value => 'Rezervované',
                VehicleAvailability::Rented->value => 'Vypůjčené',
            ]);

        $form
            ->addSelect('fuelType', 'Palivo:')
            ->setPrompt('--- Nezáleží ---')
            ->setItems(
                array_column($this->vehicleFacade->getFuelTypeList(), 'name', 'id')
            );

        $form
            ->addSelect('color', 'Barva:')
            ->setPrompt('--- Nezáleží ---')
            ->setItems(
                array_column($this->vehicleFacade->getColorList(), 'name', 'id')
            );

        $form
            ->addFloat('costPerDayFrom', 'Cena za den od:');

        $form
            ->addFloat('costPerDayTo', 'Cena za den do:');

        $form
            ->addFloat('costPerUnitFrom', 'Cena za km od:');

        $form
            ->addFloat('costPerUnitTo', 'Cena za km do:');

        $form
            ->addSelect('sortBy', 'Řadit podle:')
            ->setItems([
                SortBy::VehicleName->value => 'Název vozidla',
                SortBy::CostPerDay->value => 'Cena za den',
                SortBy::CostPerUnit->value => 'Cena za km',
            ]);

        $form
            ->addSelect('sortMode', 'Režim řazení:')
            ->setItems([
                SortMode::Ascending->value => 'Vzestupně',
                SortMode::Descending->value => 'Sestupně',
            ]);

        $form->addSubmit('filter', 'Filtrovat')->setOption('icon', 'icon-filter');
        $form->addSubmit('reset', 'Zrušit filtry')->setOption('icon', 'icon-remove');

        return $form;
    }
}
