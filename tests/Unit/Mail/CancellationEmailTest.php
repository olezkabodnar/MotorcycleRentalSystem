<?php

namespace Tests\Unit\Mail;

use App\Event\ReservationCancelled;
use App\Mail\CancellationEmail;
use App\Mail\EmailMessage;
use PHPUnit\Framework\TestCase;

class CancellationEmailTest extends TestCase
{
    public function testEmailSaysTheReservationIsCancelled()
    {
        $event = new ReservationCancelled(12, 'Test Customer', 'test@example.com', '25-KE-7788');

        $email = new CancellationEmail($event);

        $this->assertInstanceOf(EmailMessage::class, $email);
        $this->assertSame('test@example.com', $email->to());
        $this->assertStringContainsString('cancelled', $email->subject());
        $this->assertStringContainsString('Test Customer', $email->body());
        $this->assertStringContainsString('#12', $email->body());
        $this->assertStringContainsString('25-KE-7788', $email->body());
    }
}
