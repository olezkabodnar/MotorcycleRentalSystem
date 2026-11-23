<?php

namespace App\Model;

class Extra
{
    public $id;
    public $name;
    public $price;
    public $chargeType;

    public function __construct($name, $price, $chargeType, $id = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->chargeType = $chargeType;
    }
}
