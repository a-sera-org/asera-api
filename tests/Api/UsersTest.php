<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class UsersTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    public const USERNAME = 'doda';
    public const PASSWORD = '@theP*ss2023';

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testCreateUser(): void
    {
        static::createClient()->request('POST', '/users', [
            'json' => [
                'lastname' => 'Kévin',
                'username' => self::USERNAME,
                'plainPassword' => self::PASSWORD
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            '@context' => '/contexts/User',
            '@type' => 'User',
            'lastname' => 'Kévin',
            'username' => self::USERNAME
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testListUser(): void
    {
        static::createClient()->request('GET', '/users', [
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '@context' => '/contexts/User',
            "@type"=> "hydra:Collection",
        ]);
    }
}
