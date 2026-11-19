<?php

namespace App\Pricing;

// Strategy: common interface for all pricing rules
interface PricingStrategy
{
    public function calculate($rate, $start, $end);
}
