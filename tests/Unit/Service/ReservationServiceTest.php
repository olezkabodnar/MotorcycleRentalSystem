<?php

namespace Tests\Unit\Service;

use App\Event\EventDispatcher;
use App\Event\ReservationCancelled;
use App\Event\ReservationCreated;
use App\Exception\ValidationException;
use App\Model\Extra;
use App\Pricing\PriceComponent;
use App\Pricing\PricingStrategyFactory;
use App\Service\ReservationService;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Event\SpyListener;

class ReservationServiceTest extends TestCase
{
    private $reservations;
    private $createdSpy;
    private $cancelledSpy;

    private function createService()
    {
        $this->reservations = new FakeReservationRepository();
        $this->createdSpy = new SpyListener();
        $this->cancelledSpy = new SpyListener();

        $dispatcher = new EventDispatcher();
        $dispatcher->subscribe(ReservationCreated::class, $this->createdSpy);
        $dispatcher->subscribe(ReservationCancelled::class, $this->cancelledSpy);

        return new ReservationService(
            $this->reservations,
            new FakeCustomerRepository(),
            new PricingStrategyFactory(),
            $dispatcher
        );
    }

    private function helmetAndInsurance()
    {
        return [
            new Extra('Helmet', 5.00, 'per_day'),
            new Extra('Insurance', 25.00, 'once'),
        ];
    }

    // listReservations()

    public function testListReturnsTheReservationsFromTheRepository()
    {
        $service = $this->createService();
        $this->reservations->reservations = ['first', 'second'];

        $this->assertSame(['first', 'second'], $service->listReservations());
    }

    // quote()

    public function testWorkedExampleCostsFourHundredAndFive()
    {
        // Mon 23 Nov -> Tue 1 Dec = 8 days at 50
        // 400 - 15% weekly = 340, helmet 8 x 5 = 40, insurance once = 25
        $price = $this->createService()->quote(
            50.00,
            new DateTimeImmutable('2026-11-23'),
            new DateTimeImmutable('2026-12-01'),
            $this->helmetAndInsurance()
        );

        $this->assertInstanceOf(PriceComponent::class, $price);
        $this->assertSame(405.00, $price->total());
        // rental line + one line per extra
        $this->assertCount(3, $price->describe());
    }

    public function testChargeTypeDecidesHowAnExtraIsCharged()
    {
        // Mon 23 -> Thu 26 = 3 weekdays, standard price 150
        // same price 5, but per_day = 15 and once = 5
        $extras = [
            new Extra('Helmet', 5.00, 'per_day'),
            new Extra('Luggage', 5.00, 'once'),
        ];

        $price = $this->createService()->quote(
            50.00,
            new DateTimeImmutable('2026-11-23'),
            new DateTimeImmutable('2026-11-26'),
            $extras
        );

        $this->assertSame(170.00, $price->total());
    }

    public function testNoExtrasGivesTheRentalPriceOnly()
    {
        // Fri 27 -> Mon 30 = 3 days with 2 weekend days: 150 + 10
        $price = $this->createService()->quote(
            50.00,
            new DateTimeImmutable('2026-11-27'),
            new DateTimeImmutable('2026-11-30'),
            []
        );

        $this->assertSame(160.00, $price->total());
        $this->assertCount(1, $price->describe());
    }

    public function testQuoteAndSavedPriceMatchWhenDatesHaveATime()
    {
        $service = $this->createService();
        $extras = [new Extra('Helmet', 5.00, 'per_day')];

        // Mon 15:00 -> Tue 00:00 is less than 24 hours, but both methods must count 1 day
        $start = new DateTimeImmutable('2030-01-07 15:00');
        $end = new DateTimeImmutable('2030-01-08 00:00');

        $quoted = $service->quote(50.00, $start, $end, $extras)->total();
        $service->create(10, '25-KE-7788', $start, $end, $extras);

        // 1 day x 50 + helmet 1 x 5
        $this->assertSame(55.00, $quoted);
        $this->assertSame($quoted, $this->reservations->saved[0]['Price']);
    }

    // create()

    public function testEndBeforeStartIsRejected()
    {
        $service = $this->createService();

        $this->expectException(ValidationException::class);
        $service->create(10, '25-KE-7788', new DateTimeImmutable('2030-01-10'), new DateTimeImmutable('2030-01-08'), []);
    }

    public function testSameStartAndEndIsRejected()
    {
        $service = $this->createService();

        // 0 days is not a rental
        $this->expectException(ValidationException::class);
        $service->create(10, '25-KE-7788', new DateTimeImmutable('2030-01-08'), new DateTimeImmutable('2030-01-08'), []);
    }

    public function testStartInThePastIsRejected()
    {
        $service = $this->createService();

        $this->expectException(ValidationException::class);
        $service->create(10, '25-KE-7788', new DateTimeImmutable('yesterday'), new DateTimeImmutable('tomorrow'), []);
    }

    public function testStartTodayIsAllowed()
    {
        $service = $this->createService();

        // the form sends a date without time; the legacy code compared it with new DateTime() and rejected it
        $service->create(10, '25-KE-7788', new DateTimeImmutable('today'), new DateTimeImmutable('+2 days'), []);

        $this->assertCount(1, $this->reservations->saved);
        $this->assertSame(date('Y-m-d'), $this->reservations->saved[0]['StartDate']);
    }

    public function testTimeOfDayDoesNotChangeTheNumberOfDays()
    {
        $service = $this->createService();

        // now -> tomorrow is less than 24 hours, but it is still a 1 day rental, not 0
        $service->create(10, '25-KE-7788', new DateTimeImmutable('now'), new DateTimeImmutable('tomorrow'), []);

        // 1 day at 50, or 55 if today is Saturday or Sunday
        $oneDay = 50.00;
        if (date('N') >= 6) {
            $oneDay = 55.00;
        }
        $this->assertSame($oneDay, $this->reservations->saved[0]['Price']);
    }

    public function testBookedMotorcycleIsRejectedAndNothingIsSaved()
    {
        $service = $this->createService();
        $this->reservations->available = false;

        try {
            $service->create(10, '25-KE-7788', new DateTimeImmutable('2030-01-07'), new DateTimeImmutable('2030-01-09'), []);
            $this->fail('A booked motorcycle should be rejected');
        } catch (ValidationException $e) {
            $this->assertCount(0, $this->reservations->saved);
            $this->assertCount(0, $this->createdSpy->handled);
        }
    }

    public function testValidBookingIsSavedWithItsPrice()
    {
        $service = $this->createService();
        $extras = $this->helmetAndInsurance();

        // Mon 7 Jan 2030 -> Tue 15 Jan = 8 days, same numbers as the worked example
        $id = $service->create(10, '25-KE-7788', new DateTimeImmutable('2030-01-07'), new DateTimeImmutable('2030-01-15'), $extras);

        $this->assertSame(1, $id);
        $row = $this->reservations->saved[0];
        $this->assertSame('25-KE-7788', $row['RegNo']);
        $this->assertSame('yam70', $row['MBcode']);
        $this->assertSame(10, $row['CustNo']);
        $this->assertSame('2030-01-07', $row['StartDate']);
        $this->assertSame('2030-01-15', $row['EndDate']);
        $this->assertSame(405.00, $row['Price']);
        $this->assertSame('A', $row['Status']);
        $this->assertSame($extras, $this->reservations->savedExtras[0]);
    }

    public function testValidBookingSendsReservationCreated()
    {
        $service = $this->createService();

        $id = $service->create(10, '25-KE-7788', new DateTimeImmutable('2030-01-07'), new DateTimeImmutable('2030-01-15'), $this->helmetAndInsurance());

        // Observer: the service only dispatches, the email listener is not its business
        $this->assertCount(1, $this->createdSpy->handled);
        $event = $this->createdSpy->handled[0];
        $this->assertInstanceOf(ReservationCreated::class, $event);
        $this->assertSame($id, $event->reservationId);
        $this->assertSame('Test Customer', $event->customerName);
        $this->assertSame('test@example.com', $event->customerEmail);
        $this->assertSame(405.00, $event->price);
    }

    // cancel()

    public function testCancelSetsStatusAndSendsReservationCancelled()
    {
        $service = $this->createService();
        $this->reservations->reservations[5] = ['Reservation_Id' => 5, 'RegNo' => '25-KE-7788', 'CustNo' => 10, 'Status' => 'A'];

        $service->cancel(5);

        $this->assertSame([5], $this->reservations->cancelled);
        $this->assertCount(1, $this->cancelledSpy->handled);
        $this->assertSame(5, $this->cancelledSpy->handled[0]->reservationId);
        $this->assertSame('test@example.com', $this->cancelledSpy->handled[0]->customerEmail);
    }

    public function testCancellingTwiceIsRejected()
    {
        $service = $this->createService();
        $this->reservations->reservations[5] = ['Reservation_Id' => 5, 'RegNo' => '25-KE-7788', 'CustNo' => 10, 'Status' => 'C'];

        try {
            $service->cancel(5);
            $this->fail('An already cancelled reservation should be rejected');
        } catch (ValidationException $e) {
            $this->assertCount(0, $this->reservations->cancelled);
            $this->assertCount(0, $this->cancelledSpy->handled);
        }
    }

    public function testCancellingUnknownReservationIsRejected()
    {
        $service = $this->createService();

        $this->expectException(ValidationException::class);
        $service->cancel(999);
    }
}
