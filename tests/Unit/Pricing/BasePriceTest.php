<?php

namespace Tests\Unit\Pricing;

use App\Pricing\BasePrice;
use App\Pricing\PriceComponent;
use App\Pricing\StandardDailyPricing;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class BasePriceTest extends TestCase
{
    public function testTotalComesFromTheStrategy()
    {
        // Mon 23 -> Wed 25 = 2 days x 50
        $price = new BasePrice(
            new StandardDailyPricing(),
            50.00,
            new DateTimeImmutable('2026-11-23'),
            new DateTimeImmutable('2026-11-25')
        );

        $this->assertInstanceOf(PriceComponent::class, $price);
        $this->assertSame(100.00, $price->total());
        $this->assertCount(1, $price->describe());
    }
}
