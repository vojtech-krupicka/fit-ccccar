<?php declare(strict_types=1);

namespace App\UI\Front\Home;

use App\UI\Front;

final class HomePresenter extends Front\BasePresenter
{
    public function renderDefault(): void
    {
        $this->template->pageHeading = "Vítejte na {$this->appSettings->info->company}";
        $this->addBreadcrumb('Home:default', 'Domů');
        $this->addBreadcrumb('Home:somewhere', 'Somewhere', ['id' => 11]);
        $this->addBreadcrumb('Catalog:elsewhere', 'Katalog');
    }
}
