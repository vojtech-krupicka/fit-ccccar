<?php declare(strict_types=1);

namespace App\UI\Front\Catalog;

use App\Model\CarRental\CarRentalEntity;
use App\Model\CarRental\CarRentalFacade;
use App\Model\RentalOffice\RentalOfficeFacade;
use App\Model\Vehicle\VehicleAvailability;
use App\Model\Vehicle\VehicleEntity;
use App\Model\Vehicle\VehicleFacade;
use App\Model\EntityList;
use App\Model\SortMode;
use App\UI\Front\Components\Forms\Catalog\FilterFormData;
use App\UI\Front\Components\Forms\Catalog\FilterFormFactory;
use App\UI\Front\Components\Forms\Catalog\SortBy;
use App\UI\Front\Components\Forms\Reservation\ReservationFormData;
use App\UI\Front\Components\Forms\Reservation\ReservationFormFactory;
use App\UI\Shared\Components\Menu\Pagination\PaginationControl;
use App\UI\Shared\Components\Menu\Pagination\PaginationControlFactory;
use App\UI\Front;
use Nette\Application\UI;

final class CatalogPresenter extends Front\BasePresenter
{
    /**
     * @var EntityList<VehicleEntity>
     */
    private ?EntityList $vehicleList = null;

    /**
     * @var EntityList<CarRentalEntity>
     */
    private ?EntityList $vehicleReservations = null;

    private ?FilterFormData $filterData = null;

    private ?VehicleEntity $vehicle = null;

    private const ITEMS_PER_PAGE = 8;

    #region Constructor and Lifecycle Methods

    public function __construct(
        private readonly PaginationControlFactory $paginationControlFactory,
        private readonly ReservationFormFactory $reservationFormFactory,
        private readonly FilterFormFactory $filterFormFactory,
        private readonly VehicleFacade $vehicleFacade,
        private readonly CarRentalFacade $carRentalFacade,
        private readonly RentalOfficeFacade $rentalOfficeFacade,
    ) {}

    public function beforeRender(): void
    {
        parent::beforeRender();
        $this->addBreadcrumb('Catalog:default', 'Katalog vozidel');
    }

    #endregion

    #region Action default

    public function actionDefault(
        ?int $rentalOffice = null,
        ?int $fuelType = null,
        ?int $color = null,
        ?float $costPerDayFrom = null,
        ?float $costPerDayTo = null,
        ?float $costPerUnitFrom = null,
        ?float $costPerUnitTo = null,
        ?string $availability = null,
        ?string $sortBy = null,
        ?string $sortMode = null,
        int $page = 1,
    ): void {
        if ($page < 1) {
            $this->redirect('this', ['page' => 1]);
        }

        $this->filterData = new FilterFormData(
            rentalOffice: $rentalOffice,
            fuelType: $fuelType,
            color: $color,
            costPerDayFrom: $costPerDayFrom,
            costPerDayTo: $costPerDayTo,
            costPerUnitFrom: $costPerUnitFrom,
            costPerUnitTo: $costPerUnitTo,
            availability: $availability !== null ? VehicleAvailability::tryFrom($availability) : null,
            sortBy: SortBy::tryFrom((string) $sortBy) ?? SortBy::VehicleName,
            sortMode: SortMode::tryFrom((string) $sortMode) ?? SortMode::Ascending,
        );

        $this->vehicleList = $this->vehicleFacade->getAllVehicles(
            $this->filterData->rentalOffice,
            $this->filterData->fuelType,
            $this->filterData->color,
            $this->filterData->costPerDayFrom,
            $this->filterData->costPerDayTo,
            $this->filterData->costPerUnitFrom,
            $this->filterData->costPerUnitTo,
            $this->filterData->availability,
            $this->filterData->sortBy,
            $this->filterData->sortMode,
            $page,
            itemsPerPage: self::ITEMS_PER_PAGE,
        );

        if ($page > $this->vehicleList->pageCount) {
            $this->redirect('this', ['page' => $this->vehicleList->pageCount]);
        }
    }

    public function renderDefault(): void
    {
        $this->template->pageHeading = 'Katalog vozidel';
        $this->template->vehicleList = $this->vehicleList;
    }

    public function createComponentPagination(): PaginationControl
    {
        return $this->paginationControlFactory->create(
            page: $this->vehicleList->currentPage ?? 1,
            pageCount: $this->vehicleList->pageCount ?? 1,
            totalCount: $this->vehicleList->totalCount ?? 0
        );
    }

    public function createComponentFilterForm(): UI\Form
    {
        $form = $this->filterFormFactory->create();

        if ($this->filterData !== null) {
            $form->setDefaults([
                'rentalOffice' => $this->filterData->rentalOffice,
                'fuelType' => $this->filterData->fuelType,
                'color' => $this->filterData->color,
                'costPerDayFrom' => $this->filterData->costPerDayFrom,
                'costPerDayTo' => $this->filterData->costPerDayTo,
                'costPerUnitFrom' => $this->filterData->costPerUnitFrom,
                'costPerUnitTo' => $this->filterData->costPerUnitTo,
                'availability' => $this->filterData->availability?->value,
                'sortBy' => $this->filterData->sortBy->value,
                'sortMode' => $this->filterData->sortMode->value,
            ]);
        }

        $form['reset']->onClick[] = function () {
            $this->redirect('default');
        };
        $form->onSuccess[] = function (UI\Form $form, FilterFormData $values) {
            $this->redirect('this', [
                'rentalOffice' => $values->rentalOffice,
                'fuelType' => $values->fuelType,
                'color' => $values->color,
                'costPerDayFrom' => $values->costPerDayFrom,
                'costPerDayTo' => $values->costPerDayTo,
                'costPerUnitFrom' => $values->costPerUnitFrom,
                'costPerUnitTo' => $values->costPerUnitTo,
                'availability' => ($values->availability !== null ? $values->availability->value : null),
                'sortBy' => $values->sortBy->value,
                'sortMode' => $values->sortMode->value,
            ]);
        };
        return $form;
    }

    #endregion

    #region Action detail

    public function actionDetail(int $id): void
    {
        $this->vehicle = $this->vehicleFacade->get($id);
        if ($this->vehicle === null) {
            $this->error('Vozidlo nebylo nalezeno.');
        }

        $this->vehicleReservations = $this->carRentalFacade->getByVehicleId($id);
    }

    public function renderDetail(): void
    {
        $this->template->pageHeading = 'Detail vozidla';
        if ($this->vehicle === null) {
            $this->error('Vozidlo nebylo nalezeno.');
        }

        $this->addBreadcrumb('Catalog:detail', 'Detail vozidla', ['id' => $this->vehicle->id]);

        $this->template->vehicle = $this->vehicle;
        $this->template->vehicleReservations = $this->vehicleReservations;
    }

    public function createComponentReservationForm(): UI\Form
    {
        $form = $this->reservationFormFactory->create();
        $form->onSuccess[] = $this->reservationFormSucceeded(...);

        return $form;
    }

    public function reservationFormSucceeded(UI\Form $form, ReservationFormData $values): void
    {
        $rentalOffice = $this->rentalOfficeFacade->get($values->rentalOffice);
        if ($rentalOffice === null || $rentalOffice->id === null) {
            $form->addError('Vybraná pobočka neexistuje.');
            return;
        }
        if ($this->vehicle === null || $this->vehicle->id === null) {
            $form->addError('Vozidlo nebylo nalezeno.');
            return;
        }
        if ($this->user === null || $this->user->id === null) {
            $form->addError('Uživatel není přihlášen.');
            return;
        }

        $rental = new CarRentalEntity(
            id: null,
            vehicleId: $this->vehicle->id,
            rentalOfficeId: $rentalOffice->id,
            customerId: (int) $this->user->id,
            borrowerId: null,
            recipientId: null,
            reservedAt: new \DateTime(),
            reservedFrom: $values->reserveFrom,
            reservedTo: $values->reserveTo,
        );
        $this->carRentalFacade->persist($rental);
        $this->flashMessage('Rezervace byla úspěšně vytvořena.', 'success');
        $this->redirect('this', ['id' => $this->vehicle->id]);
    }

    #endregion
}
