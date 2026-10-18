<?php

namespace Tests\Unit\Pricing;

use App\Pricing\StandardDailyPricing;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class StandardDailyPricingTest extends TestCase
{
    public function testPriceIsDaysTimesDailyRate(): void
    {
        $pricing = new StandardDailyPricing();

        // 2 days (end date not counted, same as legacy)
        $price = $pricing->calculate(
            50.00,
            new DateTimeImmutable('2026-10-19'),
            new DateTimeImmutable('2026-10-21')
        );

        $this->assertSame(100.00, $price);
    }
}
