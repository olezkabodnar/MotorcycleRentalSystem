<?php

namespace Tests\Unit\Pricing;

use App\Pricing\PricingStrategy;
use App\Pricing\PricingStrategyFactory;
use App\Pricing\StandardDailyPricing;
use App\Pricing\WeekendPricing;
use App\Pricing\WeeklyDiscountPricing;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class PricingStrategyFactoryTest extends TestCase
{
    private function createFor($start, $end)
    {
        $factory = new PricingStrategyFactory();

        return $factory->create(new DateTimeImmutable($start), new DateTimeImmutable($end));
    }

    public function testWeekdaysOnlyUseStandardPricing()
    {
        // Mon 23 -> Wed 25
        $strategy = $this->createFor('2026-11-23', '2026-11-25');

        $this->assertInstanceOf(PricingStrategy::class, $strategy);
        $this->assertInstanceOf(StandardDailyPricing::class, $strategy);
    }

    public function testRentalWithWeekendUsesWeekendPricing()
    {
        // Fri 27 -> Mon 30
        $strategy = $this->createFor('2026-11-27', '2026-11-30');

        $this->assertInstanceOf(WeekendPricing::class, $strategy);
    }

    public function testSixDaysWithWeekendIsNotWeekly()
    {
        // Mon 23 -> Sun 29 = 6 days, includes Saturday
        $strategy = $this->createFor('2026-11-23', '2026-11-29');

        $this->assertInstanceOf(WeekendPricing::class, $strategy);
    }

    public function testSevenDaysUseWeeklyDiscount()
    {
        // Mon 23 -> Mon 30 = 7 days
        $strategy = $this->createFor('2026-11-23', '2026-11-30');

        $this->assertInstanceOf(WeeklyDiscountPricing::class, $strategy);
    }

    public function testWeeklyWinsOverWeekend()
    {
        // Fri 27 Nov -> Sat 5 Dec = 8 days, includes a weekend
        $strategy = $this->createFor('2026-11-27', '2026-12-05');

        $this->assertInstanceOf(WeeklyDiscountPricing::class, $strategy);
    }
}
