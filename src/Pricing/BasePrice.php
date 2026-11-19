<?php

namespace App\Pricing;

// Decorator: the base object that extras wrap, price comes from the Strategy
class BasePrice implements PriceComponent
{
    private $strategy;
    private $rate;
    private $start;
    private $end;

    public function __construct($strategy, $rate, $start, $end)
    {
        $this->strategy = $strategy;
        $this->rate = $rate;
        $this->start = $start;
        $this->end = $end;
    }

    public function total()
    {
        return $this->strategy->calculate($this->rate, $this->start, $this->end);
    }

    public function describe()
    {
        return ['Rental: ' . $this->total()];
    }
}
