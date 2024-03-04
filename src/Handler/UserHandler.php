<?php
/**
 * @author julienrajerison5@gmail.com jul
 *
 * Date : 17/02/2024
 */

namespace App\Handler;

use App\Entity\Company;
use App\Entity\Contact;
use App\Entity\Enum\UserRoleType;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class UserHandler
{
    public function __construct(public UserPasswordHasherInterface $passwordHasher, public EntityManagerInterface $entityManager)
    {
    }

    public function handleSimpleUser(User $user): User
    {
        $user->setRoles([UserRoleType::ROLE_USER]);

        return $user;
    }

    public function handleRecruiter(User $user): User
    {
        $user->setRoles([UserRoleType::ROLE_RECRUITER]);

        return $user;
    }

    /**
     * @throws \Exception
     */
    public function updateThisUser(Request $request, User $user): void
    {
        $normalizer = new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter());
        $payload = $request->request->all();
        if (isset($request->request->all()['contact'])) {
            $contact = $normalizer->denormalize($payload['contact'], Contact::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $user->getContact()]);
            $user->setContact($contact);
            unset($payload['contact']);
        }

        if (!empty($payload)) {
            $normalizer->denormalize($payload, User::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $user]);
        }
        $this->entityManager->flush();
    }

    public function addCollaborator(User $user, Company $company): User{
        $user->setCompany($company);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
