<?php

namespace App\Mail;

// Adapter: the Target interface the app uses to send mail
interface MailerInterface
{
    public function send($to, $subject, $body);
}
