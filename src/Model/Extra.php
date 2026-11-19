<?php

namespace App\Model;

class Extra
{
    public $name;
    public $price;
    public $chargeType;

    public function __construct($name, $price, $chargeType)
    {
        $this->name = $name;
        $this->price = $price;
        $this->chargeType = $chargeType;
    }
}
