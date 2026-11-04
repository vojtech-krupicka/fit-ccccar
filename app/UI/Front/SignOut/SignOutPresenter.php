<?php declare(strict_types=1);

namespace App\UI\Front\SignOut;

use App\Core\Attributes\PostSideEffect;
use App\UI\Front;

final class SignOutPresenter extends Front\BasePresenter
{
    #[PostSideEffect]
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
