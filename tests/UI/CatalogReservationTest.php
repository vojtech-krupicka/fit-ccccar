<?php declare(strict_types=1);

namespace Tests\UI;

use App\Core\Authentication\Identity;
use App\Model\CarRental\CarRentalEntity;
use App\Model\CarRental\CarRentalFacade;
use Nette\Application\Responses\RedirectResponse;
use Tests\PresenterTestCase;

final class CatalogReservationTest extends PresenterTestCase
{
    private const POST = [
        'rentalOffice' => '1',
        'reserveFrom' => '2030-01-10',
        'reserveTo' => '2030-01-12',
        'reserve' => 'Závazně rezervovat',
    ];

    private CarRentalFacade $rentals;

    protected function setUp(): void
    {
        parent::setUp();
        $this->rentals = self::container()->getByType(CarRentalFacade::class);
    }

    public function testGuestCannotReserve(): void
    {
        $before = $this->rentals->getCount();

        $this->submitForm('Front:Catalog', 'reservationForm', self::POST, ['action' => 'detail', 'id' => 1]);

        $this->assertSame($before, $this->rentals->getCount());
    }

    public function testSignedInCustomerCanReserve(): void
    {
        $this->loginAs(new Identity(id: 1, roles: ['customer'], email: 'bilbo.baggins@example.com', firstname: 'Bilbo', surname: 'Baggins'));
        $before = $this->rentals->getCount();

        $response = $this->submitForm('Front:Catalog', 'reservationForm', self::POST, ['action' => 'detail', 'id' => 1]);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame($before + 1, $this->rentals->getCount());
        $reservation = $this->rentals->get($this->latestRentalId());
        $this->assertNotNull($reservation);
        $this->assertSame(1, $reservation->customerId, 'the customer comes from the login, never from the request');
        $this->assertSame(1, $reservation->vehicleId);
    }

    public function testExpiredLoginCannotReserve(): void
    {
        // an expired "stay signed in" login keeps the identity but is not logged in anymore
        $this->keepIdentityButLogOut(new Identity(id: 1, roles: ['customer'], email: 'bilbo.baggins@example.com', firstname: 'Bilbo', surname: 'Baggins'));
        $before = $this->rentals->getCount();

        $this->submitForm('Front:Catalog', 'reservationForm', self::POST, ['action' => 'detail', 'id' => 1]);

        $this->assertSame($before, $this->rentals->getCount());
    }

    public function testTheDetailListsActiveReservationsWithTheirTimes(): void
    {
        $this->rentals->persist(new CarRentalEntity(
            id: null,
            vehicleId: 1,
            rentalOfficeId: 1,
            customerId: 2,
            borrowerId: null,
            recipientId: null,
            reservedAt: new \DateTime('2031-05-20 14:35:00'),
            reservedFrom: new \DateTime('2031-06-01'),
            reservedTo: new \DateTime('2031-06-03'),
        ));

        $html = $this->renderPage($this->runPresenter('Front:Catalog', ['action' => 'detail', 'id' => 1]));

        $this->assertStringContainsString('20.05.2031 14:35', $html, 'hours and minutes, not 12-hour clock and seconds');
        $this->assertStringContainsString('01.06.2031', $html);
    }

    public function testUnknownVehicleIsNotFound(): void
    {
        $this->expectException(\Nette\Application\BadRequestException::class);
        $this->runPresenter('Front:Catalog', ['action' => 'detail', 'id' => 999999]);
    }

    private function latestRentalId(): int
    {
        return (int) self::container()->getByType(\Nette\Database\Explorer::class)
            ->table('car_rental')->max('id');
    }
}
