<?php

namespace App\Pricing;

// Decorator: wraps a price and adds the cost of one extra on top
abstract class ExtraDecorator implements PriceComponent
{
    protected $inner;
    protected $extra;
    protected $days;

    public function __construct($inner, $extra, $days)
    {
        $this->inner = $inner;
        $this->extra = $extra;
        $this->days = $days;
    }

    public function total()
    {
        return $this->inner->total() + $this->extraCost();
    }

    public function describe()
    {
        $lines = $this->inner->describe();
        $lines[] = $this->extra->name . ': ' . $this->extraCost();

        return $lines;
    }

    abstract protected function extraCost();
}
