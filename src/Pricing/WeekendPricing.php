<?php

namespace App\Pricing;

// Strategy: +10% of the daily rate for each Saturday or Sunday
class WeekendPricing implements PricingStrategy
{
    public function calculate($rate, $start, $end)
    {
        $days = $start->diff($end)->days;
        $weekendDays = $this->countWeekendDays($start, $end);

        return round($days * $rate + $weekendDays * $rate * 0.10, 2);
    }

    public function countWeekendDays($start, $end)
    {
        $count = 0;
        $day = clone $start;

        // end date is not counted, same as the day count
        while ($day < $end) {
            if ($day->format('N') >= 6) {
                $count++;
            }
            $day = $day->modify('+1 day');
        }

        return $count;
    }
}
