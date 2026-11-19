<?php

namespace App\Pricing;

// Strategy: 15% off for rentals of 7+ days
class WeeklyDiscountPricing implements PricingStrategy
{
    public function calculate($rate, $start, $end)
    {
        $days = $start->diff($end)->days;

        return round($days * $rate * 0.85, 2);
    }
}
