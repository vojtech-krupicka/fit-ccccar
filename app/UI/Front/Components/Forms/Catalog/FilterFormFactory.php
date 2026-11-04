<?php declare(strict_types=1);

namespace App\UI\Front\Components\Forms\Catalog;

use App\Model\RentalOffice\RentalOfficeFacade;
use App\Model\Vehicle\VehicleAvailability;
use App\Model\Vehicle\VehicleFacade;
use App\Model\Vehicle\VehicleSortBy;
use App\Model\SortDirection;
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
                array_column($this->rentalOfficeFacade->getAll()->toArray(), 'name', 'id')
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
                array_column($this->vehicleFacade->getFuelTypeList()->toArray(), 'name', 'id')
            );

        $form
            ->addSelect('color', 'Barva:')
            ->setPrompt('--- Nezáleží ---')
            ->setItems(
                array_column($this->vehicleFacade->getColorList()->toArray(), 'name', 'id')
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
                VehicleSortBy::VehicleName->value => 'Název vozidla',
                VehicleSortBy::CostPerDay->value => 'Cena za den',
                VehicleSortBy::CostPerUnit->value => 'Cena za km',
            ]);

        $form
            ->addSelect('sortDirection', 'Režim řazení:')
            ->setItems([
                SortDirection::Ascending->value => 'Vzestupně',
                SortDirection::Descending->value => 'Sestupně',
            ]);

        $form->addSubmit('filter', 'Filtrovat')->setOption('icon', 'icon-filter');
        $form->addSubmit('reset', 'Zrušit filtry')->setOption('icon', 'icon-remove');

        return $form;
    }
}
