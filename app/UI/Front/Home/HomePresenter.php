<?php declare(strict_types=1);

namespace App\UI\Front\Home;

use App\Model\RentalOffice\RentalOfficeEntity;
use App\Model\RentalOffice\RentalOfficeFacade;
use App\Model\EntityList;
use App\UI\Front;

final class HomePresenter extends Front\BasePresenter
{
    /**
     * @var EntityList<RentalOfficeEntity>
     */
    private EntityList $officeList;

    public function __construct(
        private RentalOfficeFacade $officeFacade,
    ) {}

    public function renderDefault(): void
    {
        $this->template->pageHeading = "Vítejte na {$this->appSettings->info->company}";
    }

    public function renderConditions(): void
    {
        $this->template->pageHeading = 'Obchodní podmínky';
        $this->addBreadcrumb('Home:conditions', 'Obchodní podmínky');
    }

    public function renderFaq(): void
    {
        $this->template->pageHeading = 'Často kladené otázky';
        $this->addBreadcrumb('Home:faq', 'Často kladené otázky');
    }

    public function renderContact(): void
    {
        $this->template->pageHeading = 'Kontakt';
        $this->addBreadcrumb('Home:contact', 'Kontakt');
    }

    public function actionOffices(): void
    {
        $this->officeList = $this->officeFacade->getAll(orderBy: 'name, city');
    }

    public function renderOffices(): void
    {
        $this->template->pageHeading = 'Naše pobočky';
        $this->addBreadcrumb('Home:offices', 'Pobočky');

        $this->template->officeList = $this->officeList;
    }
}
