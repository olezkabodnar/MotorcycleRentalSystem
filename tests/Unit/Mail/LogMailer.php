<?php

namespace Tests\Unit\Mail;

use App\Mail\MailerInterface;

// test double: keeps sent emails in memory instead of sending them
class LogMailer implements MailerInterface
{
    public $sent = [];

    public function send($to, $subject, $body)
    {
        $this->sent[] = [
            'to' => $to,
            'subject' => $subject,
            'body' => $body,
        ];
    }
}
