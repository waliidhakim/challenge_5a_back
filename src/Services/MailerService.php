<?php

namespace App\Services;

use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;


class MailerService
{
    private $mailer;

    public function __construct(MailerInterface $mailer) {
        $this->mailer = $mailer;

    }

    public function sendEmail($content): string
    {
        $email = (new TemplatedEmail())
            ->from(new Address('idirwalidhakim31@gmail.com', 'Challenge'))
            ->to(new Address('idirwalidhakim32@gmail.com'))
            ->htmlTemplate('test/emailTest.html.twig')
            ->subject('test Emails')
            ->html($content);
//            ->replyTo(new Address($this->replyTo, 'Reply'));

        try {
            $this->mailer->send($email);
            return 'Email sent successfully';  // ou autre valeur de succès que vous préférez
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

}
