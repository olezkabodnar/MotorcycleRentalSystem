<?php

namespace Tests\Integration;

use App\Kernel\Database;
use App\Model\Reservation;
use App\Repository\ReservationRepository;
use Dotenv\Dotenv;
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
}
