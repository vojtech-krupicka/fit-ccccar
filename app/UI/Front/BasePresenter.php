<?php declare(strict_types=1);

namespace App\UI\Front;

use App\Core\Attributes;

abstract class BasePresenter extends \App\UI\BasePresenter
{
    #region SignOut

    #[Attributes\Signal]
    public function actionSignOut(): void
    {
        if ($this->user->isLoggedIn()) {
            $this->redirect('SignIn:default');
        }

        $this->user->logout(true);
        $this->flashMessage('Byl jste úspěšně odhlášen.', 'info');
        $this->redirect('SignIn:default');
    }

    #endregion SignOut
}
