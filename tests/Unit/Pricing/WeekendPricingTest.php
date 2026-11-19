<?php

namespace Tests\Unit\Pricing;

use App\Pricing\WeekendPricing;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class WeekendPricingTest extends TestCase
{
    public function testEachWeekendDayCostsTenPercentMore()
    {
        $pricing = new WeekendPricing();

        // Fri 27 -> Mon 30 = 3 days (Fri, Sat, Sun), 2 weekend days
        // 3 x 50 = 150, plus 2 x 5 = 10
        $price = $pricing->calculate(
            50.00,
            new DateTimeImmutable('2026-11-27'),
            new DateTimeImmutable('2026-11-30')
        );

        $this->assertSame(160.00, $price);
    }

    public function testCountsOnlySaturdayAndSunday()
    {
        $pricing = new WeekendPricing();

        // Thu 26 Nov -> Tue 1 Dec = Thu, Fri, Sat, Sun, Mon
        $count = $pricing->countWeekendDays(
            new DateTimeImmutable('2026-11-26'),
            new DateTimeImmutable('2026-12-01')
        );

        $this->assertSame(2, $count);
    }
}
