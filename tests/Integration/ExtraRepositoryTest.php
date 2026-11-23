<?php

namespace Tests\Integration;

use App\Kernel\Database;
use App\Model\Extra;
use App\Repository\ExtraRepository;
use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;

// integration test: real SQL against the local database (needs motorcyclerental.sql imported)
class ExtraRepositoryTest extends TestCase
{
    private function createRepository()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        $database = new Database($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);

        return new ExtraRepository($database->connect());
    }

    public function testFindAllReturnsTheSeedExtras()
    {
        $extras = $this->createRepository()->findAll();

        $this->assertCount(5, $extras);
        $this->assertInstanceOf(Extra::class, $extras[0]);
        $this->assertSame(1, $extras[0]->id);
        $this->assertSame('Helmet', $extras[0]->name);
        $this->assertSame('5.00', $extras[0]->price);
        $this->assertSame('per_day', $extras[0]->chargeType);
    }

    public function testFindByIdsReturnsOnlyTheChosenExtras()
    {
        // helmet and insurance, like the worked example
        $extras = $this->createRepository()->findByIds(['1', '5']);

        $this->assertCount(2, $extras);
        $this->assertSame('Helmet', $extras[0]->name);
        $this->assertSame('Insurance', $extras[1]->name);
    }

    public function testFindByIdsIgnoresUnknownIds()
    {
        $extras = $this->createRepository()->findByIds(['99', 'abc']);

        $this->assertCount(0, $extras);
    }

    public function testFindByIdsWithNothingTickedIsEmpty()
    {
        $this->assertSame([], $this->createRepository()->findByIds([]));
    }
}
