<?php
/**
 * @author julienrajerison5@gmail.com jul
 *
 * Date : 30/12/2023
 */

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class LoginTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    /**
     * @throws TransportExceptionInterface
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws DecodingExceptionInterface
     */
    public function testLogin()
    {
        $client = static::createClient();
        $container = self::getContainer();

        $user = new User();
        $user
            ->setLastname('Kévin')
            ->setUsername('user@asera.com')
            ->setPassword($container->get('security.user_password_hasher')->hashPassword($user, '@theP*ss2023'));

        $manager = $container->get('doctrine')->getManager();
        $manager->persist($user);
        $manager->flush();

        sleep(2); // deal with manager flushing time

        $response = $client->request('POST', '/api/login_check', [
            'json' => [
                'password' => '@theP*ss2023',
                'username' => 'user@asera.com',
            ],
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);

        $json = $response->toArray();
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $json);
    }
}
