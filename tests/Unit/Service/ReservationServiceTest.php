<?php

namespace Tests\Unit\Service;

use App\Model\Extra;
use App\Pricing\PriceComponent;
use App\Pricing\PricingStrategyFactory;
use App\Service\ReservationService;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class ReservationServiceTest extends TestCase
{
    private function createService()
    {
        return new ReservationService(new PricingStrategyFactory());
    }

    public function testWorkedExampleCostsFourHundredAndFive()
    {
        // Mon 23 Nov -> Tue 1 Dec = 8 days at 50
        // 400 - 15% weekly = 340, helmet 8 x 5 = 40, insurance once = 25
        $extras = [
            new Extra('Helmet', 5.00, 'per_day'),
            new Extra('Insurance', 25.00, 'once'),
        ];

        $price = $this->createService()->quote(
            50.00,
            new DateTimeImmutable('2026-11-23'),
            new DateTimeImmutable('2026-12-01'),
            $extras
        );

        $this->assertInstanceOf(PriceComponent::class, $price);
        $this->assertSame(405.00, $price->total());
        // rental line + one line per extra
        $this->assertCount(3, $price->describe());
    }

    public function testChargeTypeDecidesHowAnExtraIsCharged()
    {
        // Mon 23 -> Thu 26 = 3 weekdays, standard price 150
        // same price 5, but per_day = 15 and once = 5
        $extras = [
            new Extra('Helmet', 5.00, 'per_day'),
            new Extra('Luggage', 5.00, 'once'),
        ];

        $price = $this->createService()->quote(
            50.00,
            new DateTimeImmutable('2026-11-23'),
            new DateTimeImmutable('2026-11-26'),
            $extras
        );

        $this->assertSame(170.00, $price->total());
    }

    public function testNoExtrasGivesTheRentalPriceOnly()
    {
        // Fri 27 -> Mon 30 = 3 days with 2 weekend days: 150 + 10
        $price = $this->createService()->quote(
            50.00,
            new DateTimeImmutable('2026-11-27'),
            new DateTimeImmutable('2026-11-30'),
            []
        );

        $this->assertSame(160.00, $price->total());
        $this->assertCount(1, $price->describe());
    }
}
