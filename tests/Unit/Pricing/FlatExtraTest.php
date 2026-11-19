<?php

namespace Tests\Unit\Pricing;

use App\Model\Extra;
use App\Pricing\BasePrice;
use App\Pricing\FlatExtra;
use App\Pricing\StandardDailyPricing;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class FlatExtraTest extends TestCase
{
    public function testAddsPriceOnce()
    {
        // Mon 23 -> Wed 25 = 2 days, base 100
        $base = new BasePrice(
            new StandardDailyPricing(),
            50.00,
            new DateTimeImmutable('2026-11-23'),
            new DateTimeImmutable('2026-11-25')
        );
        $insurance = new Extra('Insurance', 20.00, 'once');

        $price = new FlatExtra($base, $insurance, 2);

        // 100 + 20, days do not matter
        $this->assertSame(120.00, $price->total());
        $this->assertStringContainsString('Insurance', $price->describe()[1]);
    }
}
