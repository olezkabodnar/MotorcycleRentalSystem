<?php

namespace App\Mail;

use PHPMailer\PHPMailer\PHPMailer;

// Adapter: makes PHPMailer (Adaptee) fit our MailerInterface (Target)
class PhpMailerAdapter implements MailerInterface
{
    private $host;
    private $port;
    private $username;
    private $password;
    private $fromEmail;
    private $fromName;

    public function __construct($host, $port, $username, $password, $fromEmail, $fromName)
    {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
    }

    public function send($to, $subject, $body)
    {
        // a new PHPMailer for every email, so no address is left over from the last one
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = $this->host;
        $mail->Port = $this->port;
        $mail->Timeout = 10;
        if ($this->username != '') {
            $mail->SMTPAuth = true;
            $mail->Username = $this->username;
            $mail->Password = $this->password;
        }

        $mail->CharSet = 'UTF-8';
        $mail->setFrom($this->fromEmail, $this->fromName);
        $mail->addAddress($to);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
    }
}
