<?php
/**
 * @author julienrajerison5@gmail.com jul
 *
 * Date : 05/01/2024
 */

namespace App\Entity\Enum;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use Symfony\Component\Serializer\Attribute\Groups;

#[
    ApiResource(
        normalizationContext: ['groups' => ['user_role:read']]
    ),
    GetCollection(
        uriTemplate: '/enum/user_roles',
        openapi: new \ApiPlatform\OpenApi\Model\Operation(
            summary: 'Enumerates the user possible roles',
            description: 'Return all available user roles, value is not persist anywhere !'
        ),
        provider: UserRoleType::class.'::getCases'
    ),
    Get(
        uriTemplate: '/enum/user_roles/{id}',
        openapi: new \ApiPlatform\OpenApi\Model\Operation(
            summary: 'Return detailed contract value',
            description: 'Return detailed user role value passed in the URL !'
        ),
        provider: UserRoleType::class.'::getCase'
    ),
]
enum UserRoleType: string
{
    case ROLE_USER = 'ROLE_USER';
    case ROLE_RECRUITER = 'ROLE_RECRUTEUR';

    public function getId(): string
    {
        return $this->name;
    }

    #[Groups('user_role:read')]
    public function getValue(): string
    {
        return $this->value;
    }

    #[Groups('user_role:read')]
    public function getDescription(): string
    {
        return match ($this) {
            self::ROLE_USER => 'Utilisateur simple, candidat',
            self::ROLE_RECRUITER => 'Recruteur, cet utilisateur doit être lié a une entreprise'
        };
    }

    public static function getCases(): array
    {
        return self::cases();
    }

    public static function getCase(Operation $operation, array $uriVariables)
    {
        $name = $uriVariables['id'] ?? null;

        return constant(self::class."::$name");
    }
}
