<?php

namespace Services;

use Symfony\Bridge\Twig\Mime\BodyRenderer;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Mailer\EventListener\MessageListener;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;


class SMTPConnector
{
    private Mailer $mailer;

    public function __construct($twig)
    {

        try {
            $messageListener = new MessageListener(null, new BodyRenderer($twig));

            $eventDispatcher = new EventDispatcher();
            $eventDispatcher->addSubscriber($messageListener);

            $transport = Transport::fromDsn("smtp://" . SMTPUser . ":" . SMTPPassword . "@" . SMTPServer . ":" . SMTPPort, $eventDispatcher);

            $this->mailer = new Mailer($transport, null, $eventDispatcher);
        } catch (\Doctrine\DBAL\Exception $e) {
            echo ($e->getMessage() . PHP_EOL);
            exit();
        }
    }

    public function sendMail(string $email, string $username, string $template): void
    {
        $email = (new TemplatedEmail())
            ->from('welcome@test.com')
            ->to($email)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->htmlTemplate('emails/' . $template . '.twig')
            ->context([
                'username' => $username,
            ]);

        $this->mailer->send($email);
    }
}
