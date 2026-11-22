<?php

namespace App\Event;

use App\Mail\CancellationEmail;

// Factory Method: ConcreteCreator for ReservationCancelled
class SendCancellationEmail extends ReservationEmailListener
{
    protected function createMessage($event)
    {
        return new CancellationEmail($event);
    }
}
