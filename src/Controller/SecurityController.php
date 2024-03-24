<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Util\TokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

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
                    $token = TokenGenerator::generateSixDigitToken();
                    $session->set('reset_email', $email);
                    $session->set('reset_token', $token);

                    $resetPasswordUrl = $urlGenerator->generate('reset_password_confirm', [], UrlGeneratorInterface::ABSOLUTE_URL);
                    $email = (new Email())
                        ->from('noreply@email.com')
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

                    return $this->redirectToRoute('reset_password_confirm');
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
        $resetToken = $session->get('reset_token');

        if ($request->isMethod('POST')) {
            $tokenFromRequest = $request->request->get('token');

            if ($tokenFromRequest && $tokenFromRequest === $resetToken) {
                return $this->redirectToRoute('reset_password_form');
            } else {
                throw new \InvalidArgumentException('Invalid token');
            }
        }

        return $this->render('security/reset_password_confirm.html.twig');
    }

    #[Route('/reset-password-form', name: 'reset_password_form')]
    public function resetPasswordForm(SessionInterface $session): Response
    {
        $resetEmail = $session->get('reset_email');

        return $this->render('security/reset_password_form.html.twig', [
            'resetEmail' => $resetEmail,
        ]);
    }

    #[Route(path: '/reset-password-submit', name: 'reset_password_submit', methods: ['POST'])]
    public function resetPasswordSubmit(Request $request, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository, SessionInterface $session): Response
    {
        $email = $session->get('reset_email');
        $newPassword = $request->request->get('new_password');
        $confirmPassword = $request->request->get('confirm_password');

        if (null === $email) {
            throw new \InvalidArgumentException('Email cannot be null.');
        }

        $user = $userRepository->findByEmail($email);

        if (!$user) {
            throw $this->createNotFoundException('User not found.');
        }

        if ($newPassword !== $confirmPassword) {
            return new Response('New password and confirm password do not match.');
        }

        $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
        $user->setPassword($hashedPassword);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_login');
    }
}
