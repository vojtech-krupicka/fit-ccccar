<?php declare(strict_types=1);

namespace App\UI\Admin\Vehicle;

use App\Model\Vehicle\VehicleEntity;
use App\Model\Vehicle\VehicleFacade;
use App\Model\Vehicle\VehicleFilter;
use App\Model\Vehicle\VehicleSortBy;
use App\Model\EntityList;
use App\Model\PageRequest;
use App\Model\SortDirection;
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
    private VehicleFilter $filter;
    private int $itemsPerPage;

    private const PER_PAGE_OPTIONS = [5, 10, 20, 50];
    private const DEFAULT_ITEMS_PER_PAGE = 10;

    public function __construct(
        private readonly VehicleFacade $vehicleFacade,
        private readonly DatagridControlFactory $datagridControlFactory
    ) {}

    public function actionDefault(
        int $page = 1,
        int $itemsPerPage = self::DEFAULT_ITEMS_PER_PAGE,
        ?string $sortBy = null,
        ?string $sortDirection = null,
    ): void {
        $this->itemsPerPage = in_array($itemsPerPage, self::PER_PAGE_OPTIONS, true) ? $itemsPerPage : self::DEFAULT_ITEMS_PER_PAGE;

        $this->filter = new VehicleFilter(
            sortBy: VehicleSortBy::tryFrom((string) $sortBy) ?? VehicleSortBy::Id,
            sortDirection: SortDirection::tryFrom((string) $sortDirection) ?? SortDirection::Ascending,
        );
        $pageRequest = new PageRequest(page: $page, itemsPerPage: $this->itemsPerPage);
        $this->vehicleList = $this->vehicleFacade->find($this->filter, $pageRequest);
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
        $grid = $this->datagridControlFactory->create(
            'Seznam vozidel',
            // new EntityList([], 0),
            $this->vehicleList,
            'Nebyla nalezena žádná vozidla!'
        );
        $grid->addGridSignal('add', 'Přidat vozidlo', 'icon-plus-sign');
        $grid->setSortBy($this->filter->sortBy);
        $grid->setSortDirection($this->filter->sortDirection);
        $grid->setPerPageOptions(self::PER_PAGE_OPTIONS, $this->itemsPerPage);

        if (true) {
            $grid->addBulkAction(
                'activate',
                'Aktivovat vozidla',
                function (array $ids): void {
                    $idsLine = implode(', ', $ids);
                    $this->flashMessage("Vybraná vozidla byla aktivována ({$idsLine}).");
                },
                'Opravdu chcete aktivovat vybraná vozidla?'
            );
            $grid->addBulkAction(
                'deactivate',
                'Deaktivovat vozidla',
                function (array $ids): void {
                    $idsLine = implode(', ', $ids);
                    $this->flashMessage("Vybraná vozidla byla deaktivována ({$idsLine}).");
                }
            );
            $grid->addBulkAction(
                'delete',
                'Smazat vozidla',
                function (array $ids): void {
                    $idsLine = implode(', ', $ids);
                    $this->flashMessage("Vybraná vozidla byla smazána ({$idsLine}).");
                },
                'Opravdu chcete smazat vybraná vozidla?'
            );
        }

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

        $grid->addRowSignal('edit', 'Spravovat vozidlo', 'icon-edit');
        $grid
            ->addRowSideEffect('delete', 'Smazat vozidlo', 'icon-remove', severity: ActionSeverity::Danger)
            ->setConfirmMsg('Opravdu chcete smazat toto vozidlo?');

        return $grid;
    }
}
