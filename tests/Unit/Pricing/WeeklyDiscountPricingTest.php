<?php

namespace Tests\Unit\Pricing;

use App\Pricing\WeeklyDiscountPricing;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class WeeklyDiscountPricingTest extends TestCase
{
    public function testSevenDaysGetFifteenPercentOff()
    {
        $pricing = new WeeklyDiscountPricing();

        // 7 days x 50 = 350, minus 15%
        $price = $pricing->calculate(
            50.00,
            new DateTimeImmutable('2026-11-23'),
            new DateTimeImmutable('2026-11-30')
        );

        $this->assertSame(297.50, $price);
    }
}
