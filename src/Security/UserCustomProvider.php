<?php
/**
 * @author julienrajerison5@gmail.com jul
 *
 * Date : 18/02/2024
 */

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

readonly class UserCustomProvider implements UserProviderInterface
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function refreshUser(UserInterface $user)
    {
    }

    public function supportsClass(string $class)
    {
    }

    /**
     * @throws NonUniqueResultException
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        return $this->userRepository->loadUserByEmailOrUsername($identifier) ?? new User();
    }
}