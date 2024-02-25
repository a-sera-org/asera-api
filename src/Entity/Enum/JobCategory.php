<?php
/**
 * @author julienrajerison5@gmail.com jul
 *
 * Date : 03/01/2024
 */

namespace App\Entity\Enum;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use Symfony\Component\Serializer\Attribute\Groups;

#[
    ApiResource(
        normalizationContext: ['groups' => ['job_category:read']]
    ),
    GetCollection(
        uriTemplate: '/enum/job_category',
        openapi: new \ApiPlatform\OpenApi\Model\Operation(
            summary: 'Enumerates the job categories model',
            description: 'Return all available job categories model, value is not persist anywhere !'
        ),
        provider: JobCategory::class.'::getCases'
    ),
    Get(
        uriTemplate: '/enum/job_category/{id}',
        openapi: new \ApiPlatform\OpenApi\Model\Operation(
            summary: 'Return detailed category value',
            description: 'Return detailed category value passed in the URL !'
        ),
        provider: JobCategory::class.'::getCase'
    ),
]
enum JobCategory: int
{
    case SOFTWARE_ENGINEER = 1;
    case ADMIN_SYS = 2;
    case DEVOPS = 3;
    case PRODUCT_OWNER = 4;
    case IT_HARDWARE = 5;
    case COMMUNITY_MANAGER = 6;
    case ADMINISTRATION = 7;
    case AI = 8;
    case DATA = 9;

    public function getId(): string
    {
        return $this->name;
    }

    #[Groups('job_category:read')]
    public function getValue(): int
    {
        return $this->value;
    }

    public static function getCases(): array
    {
        return self::cases();
    }

    #[Groups('user_role:read')]
    public function getDescription(): string
    {
        return match ($this) {
            self::ADMINISTRATION => 'Pour les roles administratif, genre CTO/Directeur Technique etc ...',
            self::ADMIN_SYS => 'Administrateur de système et réseau',
            self::COMMUNITY_MANAGER => 'Community manager',
            self::PRODUCT_OWNER => 'Chef de projet ou Product owner',
            self::DEVOPS => 'Devops',
            self::IT_HARDWARE => 'Help Desk, etc ...',
            self::SOFTWARE_ENGINEER => 'Dev, Lead Tech etc ...',
            self::AI => 'Artificial Intelligence Engineer',
            self::DATA => 'Data manager',
        };
    }

    public static function getCase(Operation $operation, array $uriVariables)
    {
        $name = $uriVariables['id'] ?? null;

        return constant(self::class."::$name");
    }
}
