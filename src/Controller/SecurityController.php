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
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Util\TokenGenerator;

class SecurityController extends AbstractController
{
  private $userRepository;
  private $entityManager;
  private $passwordHasher;

  public function __construct(
    UserRepository $userRepository,
    EntityManagerInterface $entityManager,
    UserPasswordHasherInterface $passwordHasher,
  ) {
    $this->userRepository = $userRepository;
    $this->entityManager = $entityManager;
    $this->passwordHasher = $passwordHasher;
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

  #[Route('/reset-password', name: 'reset_password')]
  public function resetPassword(Request $request, SessionInterface $session, UrlGeneratorInterface $urlGenerator, UserRepository $userRepository, MailerInterface $mailer): Response
  {
    if ($request->isMethod('POST')) {
      $email = $request->request->get('email');

      if ($email) {
        $email = trim($email);
        $user = $userRepository->findByEmail($email);

        if ($user) {
          // Générer un token pour la réinitialisation du mot de passe
          $token = TokenGenerator::generateSixDigitToken();
          // Stocker le token en session
          $session->set('reset_token', $token);

          // Envoyer l'e-mail de réinitialisation du mot de passe
          $resetPasswordUrl = $urlGenerator->generate('reset_password_confirm', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
          $email = (new Email())
          ->from('noreply@example.com')
          ->to($email)
          ->subject('Réinitialisation du mot de passe')
          ->html($this->renderView('/security/email/reset_password.html.twig', [
            'resetPasswordUrl' => $resetPasswordUrl,
            'token' => $token,
          ]));

          $transport = Transport::fromDsn($_ENV['MAILER_DSN']);
          $mailer = new Mailer($transport);
          $mailer->send($email);

          $this->addFlash('success', 'Un lien de réinitialisation du mot de passe a été envoyé à votre adresse e-mail.');

          return $this->redirectToRoute('reset_password_confirm', ['token' => $token]);
        } else {
          $this->addFlash('error', 'Adresse e-mail introuvable.');
        }
      } else {
        $this->addFlash('error', 'Le champ d\'e-mail est requis.');
      }
    }

    return $this->render('security/reset_password.html.twig');
  }
  #[Route('/reset-password/confirm', name: 'reset_password_confirm')]
    public function confirmResetPassword(Request $request, SessionInterface $session): Response
    {
        if ($request->isMethod('POST')) {
            $tokenFromRequest = $request->request->get('token');
            $resetToken = $session->get('reset_token');

            if ($tokenFromRequest && $tokenFromRequest === $resetToken) {
                // Implémentez ici la logique pour gérer la confirmation de la réinitialisation du mot de passe
                return $this->redirectToRoute('reset_password_form');
            } else {
                throw new \InvalidArgumentException('Invalid token');
            }
        }

        return $this->render('security/reset_password_confirm.html.twig');
    }
}

