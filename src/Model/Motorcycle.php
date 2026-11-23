<?php

namespace App\Model;

class Motorcycle
{
    public $regNo;
    public $mbCode;
    public $make;
    public $model;
    public $rate;
    public $status;

    public function __construct($row)
    {
        $this->regNo = $row['RegNo'];
        $this->mbCode = $row['MBcode'];
        $this->make = $row['Make'];
        $this->model = $row['Model'];
        $this->rate = $row['Rate'];
        $this->status = $row['Status'];
    }

    public function name()
    {
        return $this->make . ' ' . $this->model;
    }

    public function isActive()
    {
        return $this->status == 'A';
    }
}
