<?php

namespace App\Service;

use App\Pricing\BasePrice;
use App\Pricing\FlatExtra;
use App\Pricing\PerDayExtra;

// Service Layer: business rules for reservations (FR3, FR4, FR6)
class ReservationService
{
    private $pricingFactory;

    public function __construct($pricingFactory)
    {
        $this->pricingFactory = $pricingFactory;
    }

    // REFACTORED: Long Method - price was calculated inside the 280-line create_reservation.php
    public function quote($rate, $start, $end, $extras)
    {
        // Simple Factory picks the Strategy, BasePrice is its Context
        $strategy = $this->pricingFactory->create($start, $end);
        $price = new BasePrice($strategy, $rate, $start, $end);

        // Decorator: wrap the price once for every extra
        $days = $start->diff($end)->days;
        foreach ($extras as $extra) {
            if ($extra->chargeType == 'per_day') {
                $price = new PerDayExtra($price, $extra, $days);
            } else {
                $price = new FlatExtra($price, $extra, $days);
            }
        }

        return $price;
    }
}
