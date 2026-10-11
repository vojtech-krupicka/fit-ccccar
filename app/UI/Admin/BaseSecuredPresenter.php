<?php declare(strict_types=1);

namespace App\UI\Admin;

use App\UI\Admin;
use Nette\Security;

class BaseSecuredPresenter extends Admin\BasePresenter
{
    public function startup(): void
    {
        parent::startup();

        if (!$this->user->isLoggedIn()) {
            if ($this->user->getLogoutReason() === Security\User::LogoutInactivity) {
                $this->flashMessage('Byl jste automaticky odhlášen z administračního systému z důvodu neaktivity.', 'warning');
            }

            $this->redirect('SignIn:default');
        }
    }
}
