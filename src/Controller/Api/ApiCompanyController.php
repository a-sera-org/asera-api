<?php

/**
 * @author julienrajerison5@gmail.com jul
 *
 * Date : 17/02/2024
 */

namespace App\Controller\Api;

use App\Handler\UserHandler;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class ApiCompanyController extends AbstractController
{
    public function __construct(
        private readonly UserHandler $userHandler,
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer
    ) {
    }

    public function __invoke($companyId, $collaboratorId): Response
    {
        $user = $this->userRepository->find($collaboratorId);
        if (!$user) {
            return new Response('User not found', Response::HTTP_NOT_FOUND);
        }

        if ($user->getCompany()) {
            if ($user->getCompany()->getId() != $companyId) {
                return new Response('User does not belong to the specified company', Response::HTTP_BAD_REQUEST);
            }
        }

        $user->setCompany(null);
        $this->entityManager->persist($user);

        $this->entityManager->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
