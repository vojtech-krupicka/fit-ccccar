<?php declare(strict_types=1);

namespace App\UI\Admin\Components\Datagrids;

use App\Model\Vehicle\VehicleEntity;
use App\Model\Vehicle\VehicleFilter;
use App\Model\Vehicle\VehicleSortBy;
use App\Model\EntityList;
use App\UI\Shared\Components\Datagrids\Datagrid\Types\ActionSeverity;
use App\UI\Shared\Components\Datagrids\Datagrid\Types\ColumnWidth;
use App\UI\Shared\Components\Datagrids\Datagrid\Types\TextAlign;
use App\UI\Shared\Components\Datagrids\Datagrid\DatagridControl;
use App\UI\Shared\Components\Datagrids\Datagrid\DatagridControlFactory;

class VehicleDefaultDatagridFactory
{
    public function __construct(
        private readonly DatagridControlFactory $datagridFactory,
    ) {}

    /**
     * @param EntityList<VehicleEntity> $entityList
     * @param list<int> $perPageOptions
     * @param int $itemsPerPage
     * @return DatagridControl<VehicleEntity>
     */
    public function create(
        EntityList $entityList,
        VehicleFilter $filter,
        array $perPageOptions,
        int $itemsPerPage = 20,
    ): DatagridControl {
        $grid = $this->datagridFactory->create(
            'Seznam vozidel',
            $entityList,
            'Nebyla nalezena žádná vozidla!'
        );

        $grid->addGridSignal('add', 'Přidat vozidlo', 'icon-plus-sign');
        $grid->setSortBy($filter->sortBy);
        $grid->setSortDirection($filter->sortDirection);
        $grid->setPerPageOptions($perPageOptions, $itemsPerPage);

        $grid
            ->addIntegerColumn('id', 'ID')
            ->setSortableKey(VehicleSortBy::Id);
        $grid
            ->addCallableColumn(
                'name',
                'Název vozidla',
                fn(VehicleEntity $vehicle) => $vehicle->getFullName(),
            )
            ->setSortableKey(VehicleSortBy::VehicleName)
            ->addLinkFormatter('Vehicle:edit');
        $grid
            ->addTextColumn('plateNumber', 'SPZ')
            ->setSortableKey(VehicleSortBy::PlateNumber)
            ->setTextAlign(TextAlign::Center)
            ->setWidth(ColumnWidth::Width01);
        $grid
            ->addFloatColumn('mileage', 'Ujeto (km)')
            ->setSortableKey(VehicleSortBy::Mileage)
            ->setWidth(ColumnWidth::Width01);
        $grid
            ->addIntegerColumn('year', 'Rok výroby')
            ->setSortableKey(VehicleSortBy::Year)
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
            ->setWidth(ColumnWidth::Width01)
            ->setTextAlign(TextAlign::Center)
            ->addColorFormatter('color.hex')
            ->addLinkFormatter(
                'colorEdit',
                fn(VehicleEntity $vehicle) => ['id' => $vehicle->color?->id]
            );
        $grid
            ->addFloatColumn('costPerDay', 'Cena/den (Kč)')
            ->setSortableKey(VehicleSortBy::CostPerDay)
            ->setWidth(ColumnWidth::Width01);
        $grid
            ->addFloatColumn('costPerUnit', 'Cena/km (Kč)')
            ->setSortableKey(VehicleSortBy::CostPerUnit)
            ->setWidth(ColumnWidth::Width01);

        $grid
            ->addRowSignal('edit', 'Spravovat vozidlo', 'icon-edit');
        $grid
            ->addRowSideEffect('delete', 'Smazat vozidlo', 'icon-remove', severity: ActionSeverity::Danger)
            ->setConfirmMsg('Opravdu chcete smazat toto vozidlo?');

        return $grid;
    }
}
