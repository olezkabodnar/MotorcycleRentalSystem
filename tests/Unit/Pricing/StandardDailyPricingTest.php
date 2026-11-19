<?php

namespace Tests\Unit\Pricing;

use App\Pricing\StandardDailyPricing;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class StandardDailyPricingTest extends TestCase
{
    public function testPriceIsDaysTimesDailyRate()
    {
        $pricing = new StandardDailyPricing();

        // 2 days (end date not counted, same as legacy)
        $price = $pricing->calculate(
            50.00,
            new DateTimeImmutable('2026-11-23'),
            new DateTimeImmutable('2026-11-25')
        );

        $this->assertSame(100.00, $price);
    }
}
