<?php

namespace App\Pricing;

// Decorator: common interface for the base price and every extra
interface PriceComponent
{
    public function total();

    public function describe();
}
