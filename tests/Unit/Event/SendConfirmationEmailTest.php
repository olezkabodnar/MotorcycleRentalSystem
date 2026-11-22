<?php

namespace Tests\Unit\Event;

use App\Event\Listener;
use App\Event\ReservationCreated;
use App\Event\SendConfirmationEmail;
use App\Mail\ConfirmationEmail;
use Tests\Unit\Mail\LogMailer;
use PHPUnit\Framework\TestCase;

class SendConfirmationEmailTest extends TestCase
{
    private function createdEvent()
    {
        return new ReservationCreated(12, 'Test Customer', 'test@example.com', '25-KE-7788', '2026-11-23', '2026-12-01', 405.00);
    }

    public function testSendsTheConfirmationEmail()
    {
        $mailer = new LogMailer();
        $listener = new SendConfirmationEmail($mailer);
        $event = $this->createdEvent();

        $listener->handle($event);

        // the listener sends the message made by its factory method
        $expected = new ConfirmationEmail($event);
        $this->assertInstanceOf(Listener::class, $listener);
        $this->assertCount(1, $mailer->sent);
        $this->assertSame($expected->to(), $mailer->sent[0]['to']);
        $this->assertSame($expected->subject(), $mailer->sent[0]['subject']);
        $this->assertSame($expected->body(), $mailer->sent[0]['body']);
    }

    public function testMailFailureIsCaughtAndLogged()
    {
        // send the error log to a temp file instead of the console
        $logFile = tempnam(sys_get_temp_dir(), 'mail');
        $oldLog = ini_set('error_log', $logFile);
        $listener = new SendConfirmationEmail(new FailingMailer());

        $listener->handle($this->createdEvent());

        ini_set('error_log', $oldLog);
        // reached this line, so the exception did not escape
        $this->assertStringContainsString('Mail server is down', file_get_contents($logFile));
        unlink($logFile);
    }
}
