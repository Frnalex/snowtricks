<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class Mailer
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmailVerification($email, $token)
    {
        $email = (new TemplatedEmail())
            ->from(new Address('admin@snowtricks.com', 'Snowtricks'))
            ->to(new Address($email))
            ->subject('Veuillez confirmer votre email')
            ->htmlTemplate('authentication/confirmation_email.html.twig')

            // pass variables to the template
            ->context([
                'token' => $token,
            ])
        ;

        $this->mailer->send($email);
    }

    public function sendForgotPasswordEmail($email, $token)
    {
        $email = (new TemplatedEmail())
            ->from(new Address('admin@snowtricks.com', 'Snowtricks'))
            ->to(new Address($email))
            ->subject('Modification de votre mot de passe')
            ->htmlTemplate('authentication/forgot_password_email.html.twig')

            // pass variables to the template
            ->context([
                'token' => $token,
            ])
        ;

        $this->mailer->send($email);
    }
}
