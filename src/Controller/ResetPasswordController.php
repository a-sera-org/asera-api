<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ResetPasswordController extends AbstractController
{
  private $userRepository;
  private $mailer;

  public function __construct(UserRepository $userRepository, MailerInterface $mailer)
  {
    $this->userRepository = $userRepository;
    $this->mailer = $mailer;
  }

  #[Route('/reset-password', name: 'reset_password')]
  public function resetPassword(Request $request, SessionInterface $session, UrlGeneratorInterface $urlGenerator): Response
  {
    $email = $request->request->get('email');
    dump($email);
    $email = trim($email);
    $user = $this->userRepository->findByEmail($email);

    if ($user) {
      // Generate a token for password reset
      $token = Uuid::v4();
      // Store the token in the session
      $session->set('reset_token', $token);

      // Send password reset email
      $resetPasswordUrl = $urlGenerator->generate('reset_password_confirm', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
      $email = (new Email())
      ->from('hei.njaina.2@gmail.com')
      ->to($email)
      ->subject('Password Reset')
      ->html($this->renderView('email/reset_password.html.twig', [
        'resetPasswordUrl' => $resetPasswordUrl,
      ]));

      $this->mailer->send($email);

      $this->addFlash('success', 'Password reset link has been sent to your email.');

      return $this->redirectToRoute('password_reset_requested');
    }

    $this->addFlash('error', 'Email not found.');

    return $this->redirectToRoute('reset_password');
  }

  #[Route('/reset-password/requested', name: 'password_reset_requested')]
  public function passwordResetRequested(): Response
  {
    return $this->render('password_reset_requested.html.twig');
  }

  #[Route('/reset-password/confirm/{token}', name: 'reset_password_confirm')]
  public function confirmResetPassword($token, SessionInterface $session): Response
  {
    // Retrieve token from session
    $resetToken = $session->get('reset_token');

    // Check if token matches
    if ($token !== $resetToken) {
      throw new \InvalidArgumentException('Invalid token');
    }

    // Implement logic to handle password reset confirmation

    return $this->redirectToRoute('reset_password_form');
  }
}
