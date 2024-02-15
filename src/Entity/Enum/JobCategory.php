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
    case PRODUCT_OWNER = 5;
    case IT_HARDWARE = 6;
    case COMMUNITY_MANAGER = 7;
    case ADMINISTRATION = 8;

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

    public static function getCase(Operation $operation, array $uriVariables)
    {
        $name = $uriVariables['id'] ?? null;

        return constant(self::class."::$name");
    }
}
