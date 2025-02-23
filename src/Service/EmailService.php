<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class EmailService
{
    public function __construct(
        private MailerInterface $mailer
    )
    {
    }

    public function sendEmailToAdmins($subject, $template, $params = [])
    {
        $email = (new TemplatedEmail())
            ->from(new Address('admin@bouffon.fr', 'Le site du bouffon'))
            ->to('contactbouffon@gmail.com')
            ->subject('Admin Bouffon : ' . $subject)
            ->htmlTemplate($template)
            ->context($params)
        ;

        $this->mailer->send($email);
    }

    public function sendMailTo($address, $subject, $template, $params = [])
    {
        $email = (new TemplatedEmail())
            ->from(new Address('contactbouffon@gmail.com', 'Le Bouffon Théâtre'))
            ->to($address)
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($params)
        ;

        $this->mailer->send($email);
    }
}
