<?php declare(strict_types=1);

namespace App\UI\Front\SignUp;

use App\UI\Front;

final class SignUpPresenter extends Front\BasePresenter
{
    public function actionDefault(): void
    {
        if ($this->user->isLoggedIn()) {
            $this->redirect('Home:default');
        }
    }

    public function renderDefault(): void
    {
        $this->template->pageHeading = "Registrace do {$this->appSettings->info->company}";
        $this->addBreadcrumb('SignUp:default', 'Registrace');
    }
}
