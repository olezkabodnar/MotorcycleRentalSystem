<?php

namespace Tests\Integration;

use App\Kernel\Database;
use App\Model\Extra;
use App\Model\Reservation;
use App\Repository\ReservationRepository;
use DateTimeImmutable;
use Dotenv\Dotenv;
use Exception;
use PHPUnit\Framework\TestCase;

// integration test: real SQL against the local database (needs motorcyclerental.sql imported)
class ReservationRepositoryTest extends TestCase
{
    private $pdo;

    private function createRepository()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        $database = new Database($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
        $this->pdo = $database->connect();

        return new ReservationRepository($this->pdo);
    }

    public function testFindAllReturnsEveryReservation()
    {
        $reservations = $this->createRepository()->findAll();

        // a reservation whose customer or motorcycle is missing would drop out of the JOIN
        $total = $this->pdo->query('SELECT COUNT(*) FROM reservation')->fetchColumn();
        $this->assertCount((int) $total, $reservations);
        $this->assertInstanceOf(Reservation::class, $reservations[0]);
    }

    public function testFindAllHasCustomerAndMotorcycleDetails()
    {
        $reservations = $this->createRepository()->findAll();

        // seed reservation 19: Test Customer4 on the BMW R nineT 25-C-4322
        $found = null;
        foreach ($reservations as $reservation) {
            if ($reservation->id == 19) {
                $found = $reservation;
            }
        }

        $this->assertNotNull($found);
        $this->assertSame('Test Customer4', $found->customerName);
        $this->assertSame('BMW R nineT', $found->motorcycleName);
        $this->assertSame('25-C-4322', $found->regNo);
        $this->assertSame('2025-04-25', $found->startDate);
        $this->assertSame('2025-04-26', $found->endDate);
        $this->assertSame('80.00', $found->price);
        $this->assertTrue($found->isActive());
    }

    public function testFindAllShowsNewestStartDateFirst()
    {
        $reservations = $this->createRepository()->findAll();

        for ($i = 1; $i < count($reservations); $i++) {
            $this->assertGreaterThanOrEqual($reservations[$i]->startDate, $reservations[$i - 1]->startDate);
        }
    }

    // findMotorcycle()

    public function testFindMotorcycleHasTheDailyRate()
    {
        $motorcycle = $this->createRepository()->findMotorcycle('25-KE-7788');

        $this->assertSame('yam70', $motorcycle['MBcode']);
        $this->assertSame('Yamaha', $motorcycle['Make']);
        $this->assertSame('50.00', $motorcycle['Rate']);
    }

    public function testUnknownMotorcycleReturnsFalse()
    {
        $this->assertFalse($this->createRepository()->findMotorcycle('NO-SUCH-REG'));
    }

    // isAvailable() - seed reservation 23: 25-KE-7788 from 2025-05-06 to 2025-05-07, active

    private function isAvailable($regNo, $start, $end)
    {
        return $this->createRepository()->isAvailable($regNo, new DateTimeImmutable($start), new DateTimeImmutable($end));
    }

    public function testOverlappingDatesAreNotAvailable()
    {
        $this->assertFalse($this->isAvailable('25-KE-7788', '2025-05-05', '2025-05-07'));
    }

    public function testNewBookingCanStartOnTheReturnDay()
    {
        // the end date is the return day, so the bike is free again that day
        $this->assertTrue($this->isAvailable('25-KE-7788', '2025-05-07', '2025-05-09'));
    }

    public function testNewBookingCanEndOnTheStartDay()
    {
        $this->assertTrue($this->isAvailable('25-KE-7788', '2025-05-04', '2025-05-06'));
    }

    public function testBookingInsideALongerOneIsNotAvailable()
    {
        // seed reservation 25: 25-C-4321 from 2025-05-23 to 2025-05-30
        $this->assertFalse($this->isAvailable('25-C-4321', '2025-05-24', '2025-05-26'));
    }

    public function testCancelledBookingDoesNotBlockTheDates()
    {
        // seed reservation 24 is cancelled: 25-C-4321 from 2025-05-06 to 2025-05-08
        $this->assertTrue($this->isAvailable('25-C-4321', '2025-05-06', '2025-05-08'));
    }

    public function testOtherMotorcycleOnTheSameDatesIsAvailable()
    {
        $this->assertTrue($this->isAvailable('25-WW-3344', '2025-05-06', '2025-05-07'));
    }

    // insert()

    private function bookingRow()
    {
        return [
            'RegNo' => '25-WW-3344',
            'MBcode' => 'ducm8',
            'CustNo' => 10,
            'StartDate' => '2030-02-04',
            'EndDate' => '2030-02-06',
            'Price' => 170.00,
            'Status' => 'A',
        ];
    }

    private function deleteTestBookings()
    {
        // remove what the insert tests added, extras first because of the foreign key
        $this->pdo->exec("DELETE re FROM reservation_extra re JOIN reservation r ON r.Reservation_Id = re.Reservation_Id
                          WHERE r.RegNo = '25-WW-3344' AND r.StartDate = '2030-02-04'");
        $this->pdo->exec("DELETE FROM reservation WHERE RegNo = '25-WW-3344' AND StartDate = '2030-02-04'");
    }

    public function testInsertSavesTheReservationAndItsExtras()
    {
        $repository = $this->createRepository();
        $extras = [
            new Extra('Helmet', '5.00', 'per_day', 1),
            new Extra('Insurance', '25.00', 'once', 5),
        ];

        $id = $repository->insert($this->bookingRow(), $extras);

        $saved = $this->pdo->query('SELECT * FROM reservation WHERE Reservation_Id = ' . (int) $id)->fetch();
        $savedExtras = $this->pdo->query('SELECT ExtraId, Price FROM reservation_extra WHERE Reservation_Id = ' . (int) $id . ' ORDER BY ExtraId')->fetchAll();
        $this->deleteTestBookings();

        $this->assertIsInt($id);
        $this->assertSame('25-WW-3344', $saved['RegNo']);
        $this->assertSame(10, $saved['CustNo']);
        $this->assertSame('2030-02-06', $saved['EndDate']);
        $this->assertSame('170.00', $saved['Price']);
        $this->assertSame('A', $saved['Status']);
        // the extra's price is copied at booking time
        $this->assertSame([['ExtraId' => 1, 'Price' => '5.00'], ['ExtraId' => 5, 'Price' => '25.00']], $savedExtras);
    }

    public function testFailedExtraLeavesNoHalfSavedReservation()
    {
        $repository = $this->createRepository();
        // extra 99 does not exist, so the foreign key makes the second INSERT fail
        $extras = [new Extra('Ghost', '1.00', 'once', 99)];

        $failed = false;
        try {
            $repository->insert($this->bookingRow(), $extras);
        } catch (Exception $e) {
            $failed = true;
        }

        $left = $this->pdo->query("SELECT COUNT(*) FROM reservation WHERE RegNo = '25-WW-3344' AND StartDate = '2030-02-04'")->fetchColumn();
        $this->deleteTestBookings();

        $this->assertTrue($failed);
        // transaction: the reservation row was rolled back as well
        $this->assertSame(0, (int) $left);
    }
}
