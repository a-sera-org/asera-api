<?php

// Inclure l'autoloader de Composer
require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

// Configuration du transport SMTP avec MailCatcher
// $transport = Transport::fromDsn('smtp://localhost:1025');

// Créer une instance du mailer en injectant le transport

// Créer une instance de l'email
$email = (new Email())
    ->from('hei.njaina.2@gmail.com')
    ->to('njainanjaina@gmail.com')
    ->subject('Test Email')
    ->text('This is a test email sent via Symfony Mailer and MailCatcher.');

// Envoyer l'e-mail
$mailer->send($email);

echo 'L\'e-mail a été envoyé avec succès.';
