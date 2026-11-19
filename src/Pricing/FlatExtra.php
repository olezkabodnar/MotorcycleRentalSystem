<?php

namespace App\Pricing;

// Decorator: extra charged once per rental
class FlatExtra extends ExtraDecorator
{
    protected function extraCost()
    {
        return $this->extra->price;
    }
}
