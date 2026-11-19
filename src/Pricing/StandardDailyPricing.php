<?php

namespace App\Pricing;

// Strategy: normal price, days x daily rate
class StandardDailyPricing implements PricingStrategy
{
    public function calculate($rate, $start, $end)
    {
        $days = $start->diff($end)->days;

        return $days * $rate;
    }
}
