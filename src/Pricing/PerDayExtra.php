<?php

namespace App\Pricing;

// Decorator: extra charged for every rental day
class PerDayExtra extends ExtraDecorator
{
    protected function extraCost()
    {
        return $this->extra->price * $this->days;
    }
}
