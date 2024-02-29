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
        normalizationContext: ['groups' => ['job_appli_status:read']]
    ),
    GetCollection(
        uriTemplate: '/enum/job_appli_status',
        openapi: new \ApiPlatform\OpenApi\Model\Operation(
            summary: 'Enumerates the work contract model',
            description: 'Return all available contract model, value is not persist anywhere !'
        ),
        provider: JobAppliStatus::class.'::getCases'
    ),
    Get(
        uriTemplate: '/enum/job_appli_status/{id}',
        openapi: new \ApiPlatform\OpenApi\Model\Operation(
            summary: 'Return detailed contract value',
            description: 'Return detailed contract value passed in the URL !'
        ),
        provider: JobAppliStatus::class.'::getCase'
    ),
]
enum JobAppliStatus: int
{
    case RECEIVED = 1;
    case IN_PROCESS = 2;
    case REJECTED = 3;
    case ACCEPTED = 4;

    public function getId(): string
    {
        return $this->name;
    }

    #[Groups('job_appli_status:read')]
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
