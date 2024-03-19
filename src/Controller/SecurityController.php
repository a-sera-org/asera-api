<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
  private $userRepository;
  private $entityManager;
  private $passwordHasher;
  private $mailer;

  public function __construct(
    UserRepository $userRepository,
    EntityManagerInterface $entityManager,
    UserPasswordHasherInterface $passwordHasher,
    MailerInterface $mailer
  ) {
    $this->userRepository = $userRepository;
    $this->entityManager = $entityManager;
    $this->passwordHasher = $passwordHasher;
    $this->mailer = $mailer;
  }

  #[Route(path: '/login', name: 'app_login')]
  public function login(AuthenticationUtils $authenticationUtils): Response
  {
    $error = $authenticationUtils->getLastAuthenticationError();
    $lastUsername = $authenticationUtils->getLastUsername();

    return $this->render('security/login.html.twig', [
      'last_username' => $lastUsername,
      'error' => $error,
      'reset_password_route' => $this->generateUrl('reset_password'),
    ]);
  }

  #[Route(path: '/logout', name: 'app_logout')]
  public function logout(): void
  {
    throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
  }
  #[Route('/reset-password', name: 'reset_password_route')]
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
