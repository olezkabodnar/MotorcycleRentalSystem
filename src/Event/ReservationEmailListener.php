<?php

namespace App\Event;

use Throwable;

// Factory Method: Creator, subclasses decide which email to create
abstract class ReservationEmailListener implements Listener
{
    protected $mailer;

    public function __construct($mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle($event)
    {
        // a mail problem must not break the reservation
        try {
            $message = $this->createMessage($event);
            $this->mailer->send($message->to(), $message->subject(), $message->body());
        } catch (Throwable $e) {
            error_log('Reservation email failed: ' . $e->getMessage());
        }
    }

    abstract protected function createMessage($event);
}
