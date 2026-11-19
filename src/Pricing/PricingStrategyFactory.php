<?php

namespace App\Pricing;

// Simple Factory: picks the pricing rule for the rental dates
class PricingStrategyFactory
{
    public function create($start, $end)
    {
        $days = $start->diff($end)->days;

        if ($days >= 7) {
            return new WeeklyDiscountPricing();
        }

        $weekend = new WeekendPricing();
        if ($weekend->countWeekendDays($start, $end) > 0) {
            return $weekend;
        }

        return new StandardDailyPricing();
    }
}
