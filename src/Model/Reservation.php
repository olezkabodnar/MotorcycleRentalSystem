<?php

namespace App\Model;

class Reservation
{
    public $id;
    public $regNo;
    public $mbCode;
    public $custNo;
    public $startDate;
    public $endDate;
    public $price;
    public $status;
    public $customerName;
    public $motorcycleName;

    public function __construct($row)
    {
        $this->id = $row['Reservation_Id'];
        $this->regNo = $row['RegNo'];
        $this->mbCode = $row['MBcode'];
        $this->custNo = $row['CustNo'];
        $this->startDate = $row['StartDate'];
        $this->endDate = $row['EndDate'];
        $this->price = $row['Price'];
        $this->status = $row['Status'];

        // only filled when the query joins the customer and makemodel tables
        $this->customerName = '';
        if (isset($row['Forename'])) {
            $this->customerName = $row['Forename'] . ' ' . $row['Surname'];
        }
        $this->motorcycleName = '';
        if (isset($row['Make'])) {
            $this->motorcycleName = $row['Make'] . ' ' . $row['Model'];
        }
    }

    public function isActive()
    {
        return $this->status == 'A';
    }
}
