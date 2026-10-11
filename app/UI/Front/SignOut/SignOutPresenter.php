<?php declare(strict_types=1);

namespace App\UI\Front\SignOut;

use App\Core\Attributes;
use App\UI\Front;

final class SignOutPresenter extends Front\BasePresenter
{
    #[Attributes\Signal]
    public function actionDefault(): void
    {
        if (!$this->user->isLoggedIn()) {
            $this->redirect('SignIn:default');
        }

        $this->user->logout(true);
        $this->flashMessage('Byl jste úspěšně odhlášen.', 'info');
        $this->redirect('SignIn:default');
    }
}
