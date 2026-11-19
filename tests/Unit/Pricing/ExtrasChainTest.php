<?php

namespace Tests\Unit\Pricing;

use App\Model\Extra;
use App\Pricing\BasePrice;
use App\Pricing\FlatExtra;
use App\Pricing\PerDayExtra;
use App\Pricing\StandardDailyPricing;
use App\Pricing\WeeklyDiscountPricing;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class ExtrasChainTest extends TestCase
{
    public function testExtrasCanBeStacked()
    {
        // Mon 23 -> Wed 25 = 2 days, base 100
        $base = new BasePrice(
            new StandardDailyPricing(),
            50.00,
            new DateTimeImmutable('2026-11-23'),
            new DateTimeImmutable('2026-11-25')
        );

        $price = new PerDayExtra($base, new Extra('Helmet', 5.00, 'per_day'), 2);
        $price = new FlatExtra($price, new Extra('Insurance', 20.00, 'once'), 2);

        // 100 + 10 + 20
        $this->assertSame(130.00, $price->total());
        $this->assertCount(3, $price->describe());
    }

    public function testExtrasAreNotDiscounted()
    {
        // Mon 23 -> Mon 30 = 7 days, 350 - 15% = 297.50
        $base = new BasePrice(
            new WeeklyDiscountPricing(),
            50.00,
            new DateTimeImmutable('2026-11-23'),
            new DateTimeImmutable('2026-11-30')
        );

        $price = new PerDayExtra($base, new Extra('Helmet', 5.00, 'per_day'), 7);

        // 297.50 + 5 x 7, full price for the helmet
        $this->assertSame(332.50, $price->total());
    }
}
