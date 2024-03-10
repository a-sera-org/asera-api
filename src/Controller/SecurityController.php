<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
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

    #[Route('/reset-password', name: 'reset_password')]
    public function resetPasswordPage(): Response
    {
        return $this->render('security/request_password_reset.html.twig');
    }

    #[Route(path: '/reset-password-submit', name: 'reset_password_submit', methods: ['POST'])]
    public function resetPasswordSubmit(Request $request, TokenGeneratorInterface $tokenGenerator, SessionInterface $session): Response
    {
        $email = $request->request->get('email');
        $user = $this->userRepository->findOneByEmail($email);

        if ($user) {
            // Generate a token for password reset
            $token = $tokenGenerator->generateToken();
            // Store the token in the session
            $session->set('reset_token', $token);

            // Send password reset email
            $resetPasswordUrl = $this->generateUrl('reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
            $email = (new Email())
                ->from(new Address('hei.njaina.2@gmail.com', 'Asera app'))
                ->to($email)
                ->subject('Password Reset')
                ->html($this->renderView('security/reset_password_email.html.twig', [
                    'resetPasswordUrl' => $resetPasswordUrl,
                ]));

            $this->mailer->send($email);
            $this->addFlash('success', 'Password reset link has been sent to your email.');

            return $this->redirectToRoute('password_reset_requested');
        }

        $this->addFlash('error', 'Email not found.');

        return $this->redirectToRoute('reset_password');
    }

    #[Route('/password-reset-requested', name: 'password_reset_requested')]
    public function passwordResetRequested(): Response
    {
        return $this->render('security/password_reset_requested.html.twig');
    }
}
