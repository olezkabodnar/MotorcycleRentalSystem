<?php

namespace Tests\Unit\Pricing;

use App\Model\Extra;
use App\Pricing\BasePrice;
use App\Pricing\PerDayExtra;
use App\Pricing\StandardDailyPricing;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class PerDayExtraTest extends TestCase
{
    public function testAddsPriceForEachDay()
    {
        // Mon 23 -> Wed 25 = 2 days, base 100
        $base = new BasePrice(
            new StandardDailyPricing(),
            50.00,
            new DateTimeImmutable('2026-11-23'),
            new DateTimeImmutable('2026-11-25')
        );
        $helmet = new Extra('Helmet', 5.00, 'per_day');

        $price = new PerDayExtra($base, $helmet, 2);

        // 100 + 5 x 2
        $this->assertSame(110.00, $price->total());

        $lines = $price->describe();
        $this->assertCount(2, $lines);
        $this->assertStringContainsString('Helmet', $lines[1]);
    }
}
