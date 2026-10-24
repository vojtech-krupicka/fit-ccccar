<?php declare(strict_types=1);

namespace App\UI\Admin\Vehicle;

use App\Model\Vehicle\VehicleEntity;
use App\Model\Vehicle\VehicleFacade;
use App\Model\Vehicle\VehicleFilter;
use App\Model\EntityList;
use App\UI\Shared\Components\Datagrids\Datagrid\Utils\ColumnWidth;
use App\UI\Shared\Components\Datagrids\Datagrid\Utils\TextAlign;
use App\UI\Shared\Components\Datagrids\Datagrid\DatagridControl;
use App\UI\Shared\Components\Datagrids\Datagrid\DatagridControlFactory;
use App\UI\Admin;
use Nette\Application\UI;
use Nette\Utils\Html;

final class VehiclePresenter extends Admin\BaseSecuredPresenter
{
    /** @var EntityList<VehicleEntity> */
    private EntityList $vehicleList;

    public function __construct(
        private readonly VehicleFacade $vehicleFacade,
        private readonly DatagridControlFactory $datagridControlFactory
    ) {}

    public function actionDefault(): void
    {
        $this->vehicleList = $this->vehicleFacade->find(new VehicleFilter(), null);
    }

    public function renderDefault(): void
    {
        $this->template->pageHeading = 'Seznam vozidel';
        $this->addBreadcrumb('Vehicle:default', 'Seznam vozidel');
    }

    /**
     * @return DatagridControl<VehicleEntity>
     */
    public function createComponentDefaultDatagrid(): DatagridControl
    {
        $grid = $this->datagridControlFactory->create($this->vehicleList);
        $grid->addIntegerColumn('id', 'ID');
        $grid->addTextColumn('name', 'Název vozidla', fn(VehicleEntity $vehicle) => $vehicle->getFullName());
        $grid->addTextColumn('plateNumber', 'SPZ')->setTextAlign(TextAlign::Center)->setWidth(ColumnWidth::Width01);
        $grid->addFloatColumn('mileage', 'Ujeto (km)')->setWidth(ColumnWidth::Width01);
        $grid->addIntegerColumn('year', 'Rok výroby')->setWidth(ColumnWidth::Width01);
        $grid->addLinkColumn('rentalOffice.name', 'Pobočka', 'RentalOffice:default', ['id' => 'rentalOffice.id']);
        $grid->addTextColumn('fuelType.name', 'Palivo')->setWidth(ColumnWidth::Width01)->setTextAlign(TextAlign::Center);
        $grid->addTextColumn('color.name', 'Barva')->setWidth(ColumnWidth::Width01)->setTextAlign(TextAlign::Center);
        $grid->addFloatColumn('costPerDay', 'Cena/den (Kč)')->setWidth(ColumnWidth::Width01);
        $grid->addFloatColumn('costPerUnit', 'Cena/km (Kč)')->setWidth(ColumnWidth::Width01);

        // $label = Html::el()
        //     ->addText('Vozidlo')
        //     ->addHtml(Html::el('br'))
        //     ->addText('SPZ/Ujeto/Rok výroby');
        // $grid->addTextColumn('name', $label, fn(VehicleEntity $vehicle) =>
        //     "<strong>{$vehicle->getFullName()}</strong><br />"
        //     . "{$vehicle->plateNumber} / {$vehicle->mileage}km / {$vehicle->year}");

        // $label = Html::el()
        //     ->addText('Pobočka')
        //     ->addHtml(Html::el('br'))
        //     ->addText('palivo/barva');
        // $grid->addTextColumn('info', $label, fn(VehicleEntity $vehicle) =>
        //     "<strong>{$vehicle->rentalOffice->name}</strong><br />"
        //     . "{$vehicle->fuelType->name} / {$vehicle->color->name}");

        // $label = Html::el()
        //     ->addText('Cena/den')
        //     ->addHtml(Html::el('br'))
        //     ->addText('Cena/km');
        // $grid->addTextColumn('cost', $label, fn(VehicleEntity $vehicle) =>
        //     '<strong>' . number_format($vehicle->costPerDay, 2, '.', ' ') . '</strong> Kč/den<br />'
        //     . '<strong>' . number_format($vehicle->costPerUnit, 2, '.', ' ') . '</strong> Kč/km');

        return $grid;
    }
}
