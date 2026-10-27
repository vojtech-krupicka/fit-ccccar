<?php declare(strict_types=1);

namespace App\UI\Admin\RentalOffice;

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

final class RentalOfficePresenter extends Admin\BaseSecuredPresenter
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
        $this->template->pageHeading = 'Seznam poboček';
        $this->addBreadcrumb('RentalOffice:default', 'Seznam poboček');
    }
}
