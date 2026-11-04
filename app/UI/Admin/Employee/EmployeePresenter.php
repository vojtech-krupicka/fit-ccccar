<?php declare(strict_types=1);

namespace App\UI\Admin\Employee;

use App\Core\Authentication\Identity;
use App\Model\Employee\EmployeeEntity;
use App\Model\Employee\EmployeeFacade;
use App\Model\RentalOffice\RentalOfficeFacade;
use App\UI\Admin\Components\Forms\Profile\ProfileFormData;
use App\UI\Admin\Components\Forms\Profile\ProfileFormFactory;
use App\UI\Shared\Components\Forms\ChangePassword\ChangePasswordFormData;
use App\UI\Shared\Components\Forms\ChangePassword\ChangePasswordFormFactory;
use App\UI\Admin;
use \Nette\Application\UI;

final class EmployeePresenter extends Admin\BasePresenter
{
    private EmployeeEntity $employee;

    public function __construct(
        private readonly ProfileFormFactory $profileFormFactory,
        private readonly ChangePasswordFormFactory $changePasswordFormFactory,
        private readonly RentalOfficeFacade $rentalOfficeFacade,
        private readonly EmployeeFacade $employeeFacade
    ) {}

    public function startup(): void
    {
        parent::startup();

        if (!$this->user->isLoggedIn()) {
            $this->redirect('SignIn:default');
        }

        $identity = $this->user->getIdentity();
        if (!$identity instanceof Identity) {
            $this->redirect('SignIn:default');
        }

        $employee = $this->employeeFacade->get($identity->id);
        if ($employee === null) {
            $this->redirect('SignIn:default');
        }
        $this->employee = $employee;
    }

    public function renderProfile(): void
    {
        $this->template->pageHeading = 'Můj profil';
        $this->addBreadcrumb('Employee:profile', 'Můj profil');
    }

    protected function createComponentChangePasswordForm(): UI\Form
    {
        $form = $this->changePasswordFormFactory->create();

        $form['cancel']->onClick[] = function () {
            $this->getPresenter()->redirect('this');
        };
        $form->onSuccess[] = function (UI\Form $form, ChangePasswordFormData $values) {
            if ($this->employeeFacade->changePassword($this->employee, $values->oldPassword, $values->newPassword)) {
                $this->flashMessage('Vaše heslo bylo úspěšně aktualizováno.', 'info');
            } else {
                $this->flashMessage('Vaše heslo se nepodařilo změnit. Zkontrolujte prosím staré heslo.', 'error');
            }

            $this->redirect('this');
        };

        return $form;
    }

    protected function createComponentProfileForm(): UI\Form
    {
        $form = $this->profileFormFactory->create();

        $form->setDefaults([
            'email' => $this->employee->email,
            'rentalOffice' => $this->employee->rentalOfficeId,
            'firstName' => $this->employee->firstname,
            'lastName' => $this->employee->surname,
            'phone' => $this->employee->phone,
        ]);

        $form['cancel']->onClick[] = function () {
            $this->getPresenter()->redirect('this');
        };
        $form->onSuccess[] = function (UI\Form $form, ProfileFormData $values) {
            $rentalOffice = $this->rentalOfficeFacade->get($values->rentalOffice);
            if ($rentalOffice === null || $rentalOffice->id === null) {
                $form->addError('Vybraná pobočka neexistuje.');
                return;
            }

            $this->employee->email = $values->email;
            $this->employee->rentalOfficeId = $rentalOffice->id;
            $this->employee->firstname = $values->firstName;
            $this->employee->surname = $values->lastName;
            $this->employee->phone = $values->phone;

            if (!$this->employeeFacade->persist($this->employee)) {
                $form->addError('Profil se nepodařilo uložit. Zkontrolujte, zda zadaný e-mail nepoužívá jiný zaměstnanec.');
                return;
            }

            $this->flashMessage('Váš profil byl úspěšně aktualizován.', 'info');
            $this->redirect('this');
        };

        return $form;
    }
}
