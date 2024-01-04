<?php
/**
 * @author Bocasay jul
 * Date : 30/12/2023
 */

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class UserPasswordHasher.
 *
 * Hash the user password, before persist the user
 */
final readonly class UserPasswordHasher implements ProcessorInterface
{
    public function __construct(private ProcessorInterface $processor, private UserPasswordHasherInterface $passwordHasher)
    {
    }

    /**
     * @param User $data
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if (!$data->getPlainPassword()) {
            return $this->processor->process($data, $operation, $uriVariables, $context);
        }

        $hashedPassword = $this->passwordHasher->hashPassword($data, $data->getPlainPassword());
        $data->setPassword($hashedPassword);
        $data->eraseCredentials();

        return $this->processor->process($data, $operation, $uriVariables, $context);
    }
}
