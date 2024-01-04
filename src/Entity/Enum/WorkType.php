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
        normalizationContext: ['groups' => ['work_type:read']]
    ),
    GetCollection(
        uriTemplate: '/enum/work_types',
        openapi: new \ApiPlatform\OpenApi\Model\Operation(
            summary: 'JOB available work type',
            description: 'Get all work available types, this not persist anywhere, all values is statically defined'
        ),
        provider: WorkType::class.'::getCases'
    ),
    Get(
        uriTemplate: '/enum/work_types/{id}',
        openapi: new \ApiPlatform\OpenApi\Model\Operation(
            summary: 'Get single detailed work type',
            description: 'This return the value and the name of passed ID type in url'
        ),
        provider: WorkType::class.'::getCase'
    ),
]
enum WorkType: int
{
    case HYBRIDE = 1;
    case ONSITE = 2;
    case REMOTE = 3;

    public function getId(): string
    {
        return $this->name;
    }

    #[Groups('work_type:read')]
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
