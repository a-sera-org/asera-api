<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class Mailing extends AbstractController
{
    #[Route(path: '/email', name: 'send_email')]

    public function sendEmail(MailerInterface $mailer)
    {
        $email = (new Email())
            ->from('hei.njaina.2@gmail.com')
            ->to('njainanjaina@gmail.com')
            ->subject('Welcome to our platform!')
            ->html($this->renderView('emails/welcome_email.html.twig'));

        $mailer->send($email);

        return $this->redirectToRoute('success_page');
    }
}
