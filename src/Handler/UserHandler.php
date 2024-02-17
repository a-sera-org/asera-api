<?php
/**
 * @author julienrajerison5@gmail.com jul
 *
 * Date : 17/02/2024
 */

namespace App\Handler;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserHandler
{
    public function __construct(public UserPasswordHasherInterface $passwordHasher, public EntityManagerInterface $entityManager)
    {
    }

    public function handleSimpleUser(User $user): User
    {
        $user->setRoles(['ROLE_USER']);

        return $user;
    }

    public function handleRecruiter(User $user): User
    {
        $user->setRoles(['ROLE_RECRUITER']);

        return $user;
    }
}
