<?php

namespace Tests\Unit\Event;

use App\Event\ReservationCancelled;
use App\Event\SendCancellationEmail;
use App\Mail\CancellationEmail;
use Tests\Unit\Mail\LogMailer;
use PHPUnit\Framework\TestCase;

class SendCancellationEmailTest extends TestCase
{
    public function testSendsTheCancellationEmail()
    {
        $mailer = new LogMailer();
        $listener = new SendCancellationEmail($mailer);
        $event = new ReservationCancelled(12, 'Test Customer', 'test@example.com', '25-KE-7788');

        $listener->handle($event);

        $expected = new CancellationEmail($event);
        $this->assertCount(1, $mailer->sent);
        $this->assertSame($expected->to(), $mailer->sent[0]['to']);
        $this->assertSame($expected->subject(), $mailer->sent[0]['subject']);
    }
}
