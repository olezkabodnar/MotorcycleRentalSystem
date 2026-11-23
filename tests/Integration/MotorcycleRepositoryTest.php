<?php

namespace Tests\Integration;

use App\Kernel\Database;
use App\Model\Motorcycle;
use App\Repository\MotorcycleRepository;
use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;

// integration test: real SQL against the local database (needs motorcyclerental.sql imported)
class MotorcycleRepositoryTest extends TestCase
{
    private function createRepository()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        $database = new Database($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);

        return new MotorcycleRepository($database->connect());
    }

    public function testFindAllReturnsEveryMotorcycleWithMakeAndRate()
    {
        $motorcycles = $this->createRepository()->findAll();

        $this->assertCount(6, $motorcycles);
        $this->assertInstanceOf(Motorcycle::class, $motorcycles[0]);

        $found = null;
        foreach ($motorcycles as $motorcycle) {
            if ($motorcycle->regNo == '25-KE-7788') {
                $found = $motorcycle;
            }
        }
        $this->assertNotNull($found);
        $this->assertSame('Yamaha MT-07', $found->name());
        $this->assertSame('50.00', $found->rate);
        $this->assertTrue($found->isActive());
    }

    public function testFindAllIsSortedByMake()
    {
        $motorcycles = $this->createRepository()->findAll();

        $this->assertSame('BMW', $motorcycles[0]->make);
        $this->assertSame('Yamaha', $motorcycles[5]->make);
    }
}
