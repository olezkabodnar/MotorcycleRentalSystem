<?php

namespace App\Event;

use App\Mail\ConfirmationEmail;

// Factory Method: ConcreteCreator for ReservationCreated
class SendConfirmationEmail extends ReservationEmailListener
{
    protected function createMessage($event)
    {
        return new ConfirmationEmail($event);
    }
}
