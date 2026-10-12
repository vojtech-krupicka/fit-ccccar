<?php declare(strict_types=1);

namespace App\UI\Front\SignUp;

use App\UI\Front\Components\Forms\SignUp\SignUpFormFactory;
use App\UI\Front;
use Nette\Application\UI;

final class SignUpPresenter extends Front\BasePresenter
{
    public function __construct(
        private readonly SignUpFormFactory $signUpFormFactory
    ) {}

    public function actionDefault(): void
    {
        if ($this->user->isLoggedIn()) {
            $this->redirect('Home:default');
        }
    }

    public function renderDefault(): void
    {
        $this->template->pageHeading = 'Registrace uživatele';
        $this->addBreadcrumb('SignUp:default', 'Registrace');
    }

    protected function createComponentSignUpForm(): UI\Form
    {
        $form = $this->signUpFormFactory->create();

        $form->onSuccess[] = function () {
            $this->flashMessage('Registrace proběhla v pořádku, nyní se můžete přihlásit.', 'info');
            $this->redirect('SignIn:default');
        };

        return $form;
    }
}
