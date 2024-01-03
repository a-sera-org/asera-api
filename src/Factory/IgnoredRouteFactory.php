<?php
/**
 * @author julienrajerison5@gmail.com jul
 *
 * Date : 31/12/2023
 */

namespace App\Factory;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\OpenApi;
use ApiPlatform\OpenApi\Model;

/**
 * Class IgnoredRouteFactory
 *
 * Handle routing personalizing
 */
readonly class IgnoredRouteFactory implements OpenApiFactoryInterface
{
    public function __construct(private OpenApiFactoryInterface $decorated){}

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);

        $paths = $openApi->getPaths()->getPaths();

        $filteredPaths = new Model\Paths();
        foreach ($paths as $path => $pathItem) {
            // Here you go with our ignored route
            if ($path === '/user_medias') {
                continue;
            }
            $filteredPaths->addPath($path, $pathItem);
        }

        return $openApi->withPaths($filteredPaths);
    }
}