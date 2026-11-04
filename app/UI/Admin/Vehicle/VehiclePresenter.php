<?php declare(strict_types=1);

namespace App\UI\Admin\Vehicle;

use App\Core\Attributes\PostSideEffect;
use App\Model\Vehicle\VehicleEntity;
use App\Model\Vehicle\VehicleFacade;
use App\Model\Vehicle\VehicleFilter;
use App\Model\Vehicle\VehicleSortBy;
use App\Model\EntityList;
use App\Model\PageRequest;
use App\Model\SortDirection;
use App\UI\Admin\Components\Datagrids\VehicleDefaultDatagridFactory;
use App\UI\Shared\Components\Datagrids\Datagrid\DatagridControl;
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
        private readonly VehicleDefaultDatagridFactory $defaultDatagridFactory
    ) {}

    #region Other actions

    #[PostSideEffect]
    public function actionDelete(int $id): void
    {
        $vehicle = $this->vehicleFacade->get($id);
        if ($vehicle === null) {
            $this->flashMessage("Vozidlo s ID {$id} nebylo nalezeno.", 'danger');
        } else {
            $this->flashMessage("Vozidlo s ID {$id} bylo smazáno (FAKE).");
        }

        $this->redirect('default');
    }

    public function actionAdd(): void
    {
        $this->flashMessage("Funkcionalita 'přidat vozidlo' není implementována.", 'warning');
        $this->redirect('default');
    }

    public function actionEdit(int $id): void
    {
        $this->flashMessage("Funkcionalita 'upravit vozidlo #{$id}' není implementována.", 'warning');
        $this->redirect('default');
    }

    #endregion

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
        $grid = $this->defaultDatagridFactory->create(
            $this->vehicleList,
            $this->filter,
            self::PER_PAGE_OPTIONS,
            $this->itemsPerPage,
        );

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

        return $grid;
    }
}
