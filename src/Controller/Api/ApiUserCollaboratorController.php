<?php

/**
 * @author julienrajerison5@gmail.com jul
 *
 * Date : 17/02/2024
 */

namespace App\Controller\Api;

use App\Entity\User;
use App\Handler\UserHandler;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class ApiUserCollaboratorController extends AbstractController
{
    public function __construct(
        private readonly UserHandler $userHandler,
        private CompanyRepository $companyRepository,
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer
    ) {
    }

    public function __invoke(User $user, $companyId): User
    {
        $company = $this->companyRepository->find($companyId);
        if (!$company) {
            return new Response('Company not found', Response::HTTP_NOT_FOUND);
        }
        $user->setCompany($company);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}