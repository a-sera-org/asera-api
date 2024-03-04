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
        normalizationContext: ['groups' => ['job_type:read']]
    ),
    GetCollection(
        uriTemplate: '/enum/job_types',
        openapi: new \ApiPlatform\OpenApi\Model\Operation(
            summary: 'Enumerates the work contract model',
            description: 'Return all available contract model, value is not persist anywhere !'
        ),
        provider: JobType::class.'::getCases'
    ),
    Get(
        uriTemplate: '/enum/job_types/{id}',
        openapi: new \ApiPlatform\OpenApi\Model\Operation(
            summary: 'Return detailed contract value',
            description: 'Return detailed contract value passed in the URL !'
        ),
        provider: JobType::class.'::getCase'
    ),
]
enum JobType: int
{
    case CDI = 1;
    case CDD = 2;
    case CONSULTANT = 3;
    case FREELANCE = 4;
    case STAGE = 5;
    case ALTERNANT = 6;

    public function getId(): string
    {
        return $this->name;
    }

    #[Groups('job_type:read')]
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
