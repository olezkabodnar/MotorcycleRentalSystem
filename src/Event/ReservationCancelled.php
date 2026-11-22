<?php

namespace App\Event;

// Observer: event sent after a reservation is cancelled
class ReservationCancelled
{
    public $reservationId;
    public $customerName;
    public $customerEmail;
    public $regNo;

    public function __construct($reservationId, $customerName, $customerEmail, $regNo)
    {
        $this->reservationId = $reservationId;
        $this->customerName = $customerName;
        $this->customerEmail = $customerEmail;
        $this->regNo = $regNo;
    }
}
