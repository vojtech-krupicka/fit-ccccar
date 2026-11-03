<?php declare(strict_types=1);

namespace App\UI\Front\Customer;

use App\Core\Authentication\Identity;
use App\Model\CarRental\CarRentalEntity;
use App\Model\CarRental\CarRentalFacade;
use App\Model\CarRental\CarRentalFilter;
use App\Model\CarRental\CarRentalSortBy;
use App\Model\Customer\CustomerEntity;
use App\Model\Customer\CustomerFacade;
use App\Model\EntityList;
use App\UI\Front\Components\Forms\Profile\ProfileFormData;
use App\UI\Front\Components\Forms\Profile\ProfileFormFactory;
use App\UI\Shared\Components\Forms\ChangePassword\ChangePasswordFormData;
use App\UI\Shared\Components\Forms\ChangePassword\ChangePasswordFormFactory;
use App\UI\Front;
use \Nette\Application\UI;

final class CustomerPresenter extends Front\BasePresenter
{
    private CustomerEntity $customer;

    /**
     * @var EntityList<CarRentalEntity>
     */
    private ?EntityList $vehicleReservations = null;

    public function __construct(
        private readonly ProfileFormFactory $profileFormFactory,
        private readonly ChangePasswordFormFactory $changePasswordFormFactory,
        private readonly CustomerFacade $customerFacade,
        private readonly CarRentalFacade $carRentalFacade,
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

        $customer = $this->customerFacade->get($identity->id);
        if ($customer === null) {
            $this->redirect('SignIn:default');
        }
        $this->customer = $customer;
    }

    public function renderProfile(): void
    {
        $this->template->pageHeading = 'Můj profil';
        $this->addBreadcrumb('Customer:profile', 'Můj profil');
    }

    protected function createComponentChangePasswordForm(): UI\Form
    {
        $form = $this->changePasswordFormFactory->create();

        $form['cancel']->onClick[] = function () {
            $this->getPresenter()->redirect('this');
        };
        $form->onSuccess[] = function (UI\Form $form, ChangePasswordFormData $values) {
            if ($this->customerFacade->changePassword($this->customer, $values->oldPassword, $values->newPassword)) {
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
            'email' => $this->customer->email,
            'firstName' => $this->customer->firstname,
            'lastName' => $this->customer->surname,
            'idNumber' => $this->customer->identityNumber,
            'phone' => $this->customer->phone,
            'street' => $this->customer->street,
            'city' => $this->customer->city,
            'zip' => $this->customer->zip,
            'country' => $this->customer->country,
        ]);

        $form['cancel']->onClick[] = function () {
            $this->getPresenter()->redirect('this');
        };
        $form->onSuccess[] = function (UI\Form $form, ProfileFormData $values) {
            $this->customer->email = $values->email;
            $this->customer->firstname = $values->firstName;
            $this->customer->surname = $values->lastName;
            $this->customer->identityNumber = $values->idNumber;
            $this->customer->phone = $values->phone;
            $this->customer->street = $values->street;
            $this->customer->city = $values->city;
            $this->customer->zip = $values->zip;
            $this->customer->country = $values->country;

            if (!$this->customerFacade->persist($this->customer)) {
                $form->addError('Profil se nepodařilo uložit. Zkontrolujte, zda zadaný e-mail nepoužívá jiný zákazník.');
                return;
            }

            $this->flashMessage('Váš profil byl úspěšně aktualizován.', 'info');
            $this->redirect('this');
        };

        return $form;
    }

    #region Action loans

    public function actionLoans(): void
    {
        // startup() has already redirected guests and unknown customers, so $this->customer is always set
        $this->vehicleReservations = $this->carRentalFacade->find(
            new CarRentalFilter(
                customerId: $this->customer->id,
                sortBy: CarRentalSortBy::Latest,
            ),
        );
    }

    public function renderLoans(): void
    {
        $this->template->pageHeading = 'Moje půjčky';
        $this->addBreadcrumb('Customer:loans', 'Moje půjčky');

        $this->template->reservations = $this->vehicleReservations;
    }

    #endregion
}
