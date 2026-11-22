<?php

namespace Tests\Unit\Event;

use App\Mail\MailerInterface;
use Exception;

// test double that acts like the mail server is down
class FailingMailer implements MailerInterface
{
    public function send($to, $subject, $body)
    {
        throw new Exception('Mail server is down');
    }
}
