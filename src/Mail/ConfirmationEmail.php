<?php

namespace App\Mail;

// Factory Method: concrete Product for a new reservation
class ConfirmationEmail implements EmailMessage
{
    private $event;

    public function __construct($event)
    {
        $this->event = $event;
    }

    public function to()
    {
        return $this->event->customerEmail;
    }

    public function subject()
    {
        return 'Your reservation #' . $this->event->reservationId . ' is confirmed';
    }

    public function body()
    {
        $body = 'Dear ' . $this->event->customerName . ",\n\n";
        $body .= 'Your reservation #' . $this->event->reservationId . " is confirmed.\n\n";
        $body .= 'Motorcycle: ' . $this->event->motorcycleName . ' (' . $this->event->regNo . ")\n";
        $body .= 'From: ' . $this->event->startDate . "\n";
        $body .= 'To: ' . $this->event->endDate . "\n";
        $body .= 'Total price: €' . number_format($this->event->price, 2) . "\n\n";
        $body .= 'MotoRent';

        return $body;
    }
}
