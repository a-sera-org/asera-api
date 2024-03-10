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

    public const USERNAME = 'asera';
    public const PASSWORD = '@theP*ss2023';

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testCreateUser(): void
    {
        static::createClient()->request('POST', '/api/register/user', [
            'json' => [
                'lastname' => 'Kévin',
                'username' => self::USERNAME,
                'plainPassword' => self::PASSWORD,
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
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
        static::createClient()->request('GET', '/api/users', [
            'headers' => [
                'Content-Type' => 'application/ld+json',
                'Authorization' => sprintf('Bearer %s', $this->getToken()),
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);
    }


    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getToken(): string
    {
        $client = static::createClient();
        $this->createUser();
        $response = $client->request('POST', '/api/login_check', [
            'json' => [
                'password' => '@theP*ss2023',
                'username' => 'user@asera.com',
            ],
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);
        $json = $response->toArray();

        return $json['token'];
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function createUser(): void
    {
        $container = self::getContainer();

        $user = new User();
        $user
            ->setLastname('Kévin')
            ->setUsername('user@asera.com')
            ->setPassword($container->get('security.user_password_hasher')->hashPassword($user, '@theP*ss2023'));

        $manager = $container->get('doctrine')->getManager();
        $manager->persist($user);
        $manager->flush();

        sleep(1); // deal with manager flushing time
    }
}
