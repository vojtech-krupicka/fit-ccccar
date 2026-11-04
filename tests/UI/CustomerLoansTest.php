<?php declare(strict_types=1);

namespace Tests\UI;

use App\Core\Authentication\Identity;
use App\Model\CarRental\CarRentalEntity;
use App\Model\CarRental\CarRentalRepository;
use App\Model\Customer\CustomerEntity;
use Nette\Application\Responses\RedirectResponse;
use Tests\CustomerFixture;
use Tests\PresenterTestCase;

final class CustomerLoansTest extends PresenterTestCase
{
    use CustomerFixture;

    private CustomerEntity $customer;
    private CustomerEntity $other;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customer = $this->createCustomer('loans@example.com');
        $this->other = $this->createCustomer('other@example.com');
        $this->loginAs(new Identity(
            id: (int) $this->customer->id,
            roles: ['customer'],
            email: 'loans@example.com',
            firstname: 'Test',
            surname: 'User',
        ));
    }

    private function addRental(CustomerEntity $customer, string $reservedFrom, ?string $startAt = null, ?string $endAt = null, string $reservedAt = '2031-01-01 10:00:00'): void
    {
        self::container()->getByType(CarRentalRepository::class)->insert(new CarRentalEntity(
            id: null,
            vehicleId: 1,
            rentalOfficeId: 1,
            customerId: (int) $customer->id,
            borrowerId: $startAt !== null ? 1 : null,
            recipientId: $endAt !== null ? 1 : null,
            reservedAt: new \DateTime($reservedAt),
            reservedFrom: new \DateTime($reservedFrom),
            reservedTo: new \DateTime($reservedFrom . ' +2 days'),
            startAt: $startAt !== null ? new \DateTime($startAt) : null,
            endAt: $endAt !== null ? new \DateTime($endAt) : null,
        ));
    }

    private function loansPage(): string
    {
        return $this->renderPage($this->runPresenter('Front:Customer', ['action' => 'loans']));
    }

    public function testGuestsAreSentToTheSignInPage(): void
    {
        $this->keepIdentityButLogOut(new Identity(id: (int) $this->customer->id, roles: ['customer'], email: 'x', firstname: 'x', surname: 'x'));

        $response = $this->runPresenter('Front:Customer', ['action' => 'loans']);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertStringContainsString('prihlaseni', $response->getUrl());
    }

    public function testACustomerWithoutRentalsSeesAnExplanation(): void
    {
        $this->assertStringContainsString('nejsou k dispozici žádné rezervace nebo výpůjčky', $this->loansPage());
    }

    public function testACustomerSeesOnlyTheirOwnRentals(): void
    {
        $this->addRental($this->customer, '2031-03-05');
        $this->addRental($this->other, '2031-04-06');

        $html = $this->loansPage();

        $this->assertStringContainsString('05.03.2031', $html);
        $this->assertStringNotContainsString('06.04.2031', $html, 'the rental of another customer must never be shown');
    }

    public function testTheListOpensForTheSignedInCustomerWhateverTheRequestSays(): void
    {
        $this->addRental($this->other, '2031-04-06');

        // nothing in the request can select another customer
        $html = $this->renderPage($this->runPresenter('Front:Customer', ['action' => 'loans', 'id' => $this->other->id, 'customerId' => $this->other->id]));

        $this->assertStringNotContainsString('06.04.2031', $html);
    }

    public function testReturnedRentalsStayInTheHistory(): void
    {
        $this->addRental($this->customer, '2031-05-10', startAt: '2031-05-10 09:00:00', endAt: '2031-05-12 17:00:00');

        $html = $this->loansPage();

        $this->assertStringContainsString('10.05.2031', $html, 'a finished rental is part of "my loans"');
        $this->assertStringContainsString('12.05.2031', $html, 'the return date is shown');
    }

    public function testTimesAreShownAsHoursAndMinutes(): void
    {
        $this->addRental($this->customer, '2031-06-01', reservedAt: '2031-05-20 14:35:00');

        $this->assertStringContainsString('20.05.2031 14:35', $this->loansPage());
    }

    public function testTheNewestRentalComesFirst(): void
    {
        $this->addRental($this->customer, '2031-07-01', reservedAt: '2031-01-01 10:00:00');
        $this->addRental($this->customer, '2031-08-01', reservedAt: '2031-02-01 10:00:00');

        $html = $this->loansPage();

        $this->assertLessThan(strpos($html, '01.07.2031'), strpos($html, '01.08.2031'));
    }
}
