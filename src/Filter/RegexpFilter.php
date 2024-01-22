<?php
/**
 * @author julienrajerison5@gmail.com jul
 *
 * Date : 22/01/2024
 */

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PropertyInfo\Type;

/**
 * Class RegexpFilter.
 *
 * Add custom regex filter
 */
final class RegexpFilter extends AbstractFilter
{
    /*
     * Filtered properties is accessible through getProperties() method: property => strategy
     */
    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        /*
         * Otherwise this filter is applied to order and page as well.
         */
        if (!$this->isPropertyEnabled($property, $resourceClass) || !$this->isPropertyMapped($property, $resourceClass)) {
            return;
        }

        /*
         * Generate a unique parameter name to avoid collisions with other filters.
         */
        $parameterName = $queryNameGenerator->generateParameterName($property);
        $queryBuilder
            ->andWhere(sprintf('REGEXP(o.%s, :%s) = 1', $property, $parameterName))
            ->setParameter($parameterName, $value);
    }

    /*
     * This function is only used to hook in documentation generators (supported by Swagger and Hydra).
     */
    public function getDescription(string $resourceClass): array
    {
        if (!$this->properties) {
            return [];
        }
        $description = [];
        foreach ($this->properties as $property => $strategy) {
            $description["regexp_$property"] = [
                'property' => $property,
                'type' => Type::BUILTIN_TYPE_STRING,
                'required' => false,
                'description' => "Filter $property using a regex.",
                'openapi' => [
                    'example' => "Ex : $property=value",
                    /*
                     * If true, query parameters will be not percent-encoded
                     */
                    'allowReserved' => false,
                    'allowEmptyValue' => true,
                    /*
                     * To be true, the type must be Type::BUILTIN_TYPE_ARRAY, ?product=blue,green will be ?product[]=blue&product[]=green
                     */
                    'explode' => false,
                ],
            ];
        }

        return $description;
    }
}
