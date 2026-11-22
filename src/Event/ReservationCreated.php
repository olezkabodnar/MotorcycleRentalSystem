<?php

namespace App\Event;

// Observer: event sent after a reservation is saved
class ReservationCreated
{
    public $reservationId;
    public $customerName;
    public $customerEmail;
    public $regNo;
    public $startDate;
    public $endDate;
    public $price;

    public function __construct($reservationId, $customerName, $customerEmail, $regNo, $startDate, $endDate, $price)
    {
        $this->reservationId = $reservationId;
        $this->customerName = $customerName;
        $this->customerEmail = $customerEmail;
        $this->regNo = $regNo;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->price = $price;
    }
}
