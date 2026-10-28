<?php declare(strict_types=1);

namespace App\UI\Admin\Vehicle;

use App\Model\Vehicle\VehicleEntity;
use App\Model\Vehicle\VehicleFacade;
use App\Model\Vehicle\VehicleFilter;
use App\Model\EntityList;
use App\UI\Shared\Components\Datagrids\Datagrid\Types\ActionSeverity;
use App\UI\Shared\Components\Datagrids\Datagrid\Types\ColumnWidth;
use App\UI\Shared\Components\Datagrids\Datagrid\Types\TextAlign;
use App\UI\Shared\Components\Datagrids\Datagrid\DatagridControl;
use App\UI\Shared\Components\Datagrids\Datagrid\DatagridControlFactory;
use App\UI\Admin;

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
        $grid
            ->addCallableColumn(
                'name',
                'Název vozidla',
                fn(VehicleEntity $vehicle) => $vehicle->getFullName(),
            )
            ->addLinkFormatter('Vehicle:edit');
        $grid
            ->addTextColumn('plateNumber', 'SPZ')
            ->setTextAlign(TextAlign::Center)
            ->setWidth(ColumnWidth::Width01);
        $grid
            ->addFloatColumn('mileage', 'Ujeto (km)')
            ->setWidth(ColumnWidth::Width01);
        $grid
            ->addIntegerColumn('year', 'Rok výroby')
            ->setWidth(ColumnWidth::Width01);
        $grid
            ->addTextColumn('rentalOffice', 'Pobočka', 'rentalOffice.name')
            ->addLinkFormatter(
                'RentalOffice:default',
                fn(VehicleEntity $vehicle) => ['id' => $vehicle->rentalOffice?->id]
            );
        $grid
            ->addTextColumn('fuelType.name', 'Palivo')
            ->setWidth(ColumnWidth::Width01)
            ->setTextAlign(TextAlign::Center)
            ->addLinkFormatter(
                'fuelTypeEdit',
                fn(VehicleEntity $vehicle) => ['id' => $vehicle->fuelType?->id]
            );
        $grid
            ->addTextColumn('color', 'Barva', 'color.name')
            ->addColorFormatter('color.hex')
            ->addLinkFormatter(
                'colorEdit',
                fn(VehicleEntity $vehicle) => ['id' => $vehicle->color?->id]
            );
        $grid
            ->addFloatColumn('costPerDay', 'Cena/den (Kč)')
            ->setWidth(ColumnWidth::Width01);
        $grid
            ->addFloatColumn('costPerUnit', 'Cena/km (Kč)')
            ->setWidth(ColumnWidth::Width01);

        $grid->addSignal('edit', 'Spravovat vozidlo', 'icon-edit');
        $grid
            ->addSideEffect('delete', 'Smazat vozidlo', 'icon-remove', severity: ActionSeverity::Danger)
            ->setConfirmMsg('Opravdu chcete smazat toto vozidlo?');

        return $grid;
    }
}
