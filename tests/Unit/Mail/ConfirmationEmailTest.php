<?php

namespace Tests\Unit\Mail;

use App\Event\ReservationCreated;
use App\Mail\ConfirmationEmail;
use App\Mail\EmailMessage;
use PHPUnit\Framework\TestCase;

class ConfirmationEmailTest extends TestCase
{
    public function testEmailHasTheReservationDetails()
    {
        $event = new ReservationCreated(12, 'Test Customer', 'test@example.com', '25-KE-7788', '2026-11-23', '2026-12-01', 405.00, 'Yamaha MT-07');

        $email = new ConfirmationEmail($event);

        $this->assertInstanceOf(EmailMessage::class, $email);
        $this->assertSame('test@example.com', $email->to());
        $this->assertStringContainsString('confirmed', $email->subject());
        $this->assertStringContainsString('Test Customer', $email->body());
        $this->assertStringContainsString('Yamaha MT-07 (25-KE-7788)', $email->body());
        $this->assertStringContainsString('2026-11-23', $email->body());
        $this->assertStringContainsString('2026-12-01', $email->body());
        $this->assertStringContainsString('405.00', $email->body());
    }
}
