<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private $userRepository;
    private $passwordHasher;
    private $entityManager;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
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
    public function resetPasswordPage(): Response
    {
        return $this->render('security/reset_password.html.twig');
    }

    #[Route(path: '/reset-password-submit', name: 'reset_password_submit', methods: ['POST'])]
    public function resetPasswordSubmit(Request $request): Response
    {
        $username = $request->request->get('username');
        $newPassword = $request->request->get('new_password');
        $confirmPassword = $request->request->get('confirm_password');

        $user = $this->userRepository->findByUsername($username);

        if (!$user) {
            throw $this->createNotFoundException('User not found.');
        }

        if ($newPassword !== $confirmPassword) {
            return new Response('New password and confirm password do not match.');
        }

        $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
        $user->setPassword($hashedPassword);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_login');
    }
}
