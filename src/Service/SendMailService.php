<?php
namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;


class SendMailService{
    private $mailer;
    public function __construct(MailerInterface $mailer){
        $this->mailer = $mailer;
    }

    public function send(

        string $from,
        string $to,
        string $subject,
        string $template,
        array $context
    ): void
    {
        //Création du mail

        $email = (new TemplatedEmail())
            -> from($from)
            -> to($to)
            -> subject($subject)
            -> htmlTemplate("email/$template.html.twig")
            -> context($context);

        // On envoie le Mail
        $this->mailer->send($email);
    }

    public function sendContactMail(string $from,
        string $to,
        string $subject,
        string $template,
        array $context
    ): void {
        $email = (new TemplatedEmail())
            -> from($from)
            -> to($to)
            -> subject($subject)
            -> htmlTemplate("email/$template.html.twig")
            -> context($context);

        // On envoie le Mail
        $this->mailer->send($email);
    }
}