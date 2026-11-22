<?php

namespace App\Mail;

// Factory Method: concrete Product for a cancelled reservation
class CancellationEmail implements EmailMessage
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
        return 'Your reservation #' . $this->event->reservationId . ' is cancelled';
    }

    public function body()
    {
        $body = 'Dear ' . $this->event->customerName . ",\n\n";
        $body .= 'Your reservation #' . $this->event->reservationId;
        $body .= ' for motorcycle ' . $this->event->regNo . " has been cancelled.\n\n";
        $body .= 'MotoRent';

        return $body;
    }
}
