<?php

namespace Tests\Integration;

use App\Kernel\Database;
use App\Model\Customer;
use App\Repository\CustomerRepository;
use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;

// integration test: real SQL against the local database (needs motorcyclerental.sql imported)
class CustomerRepositoryTest extends TestCase
{
    private function createRepository()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        $database = new Database($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);

        return new CustomerRepository($database->connect());
    }

    public function testFindsCustomerById()
    {
        // seed customer 27
        $customer = $this->createRepository()->findById(27);

        $this->assertInstanceOf(Customer::class, $customer);
        $this->assertSame('Customer2', $customer->surname);
        $this->assertSame('test2@example.com', $customer->email);
    }

    public function testUnknownIdReturnsFalse()
    {
        $this->assertFalse($this->createRepository()->findById(99));
    }
}
