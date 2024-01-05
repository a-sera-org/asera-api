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

class CompanyTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testFailedCreateCompanies(): void
    {
        static::createClient()->request('POST', '/companies', [
            'json' => [
                'name' => 'bonbon',
                'address' => 'Tana',
                'contacts' => [
                    [
                        'email' => 'test@company.com',
                        'phone' => ['097 876 76', '097 876 78', '097 876 77'],
                        'web' => 'company.com',
                    ],
                ],
                'nif' => '34557 7889 9800',
                'stat' => 'Madagascar',
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(401); // test will 401, you can't create company without authenticated token.
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface|DecodingExceptionInterface
     */
    public function testSuccessCreateCompanies(): void
    {
        $client = static::createClient();
        $client->request('POST', '/companies', [
            'json' => [
                'name' => 'bonbon',
                'address' => 'Tana',
                'contacts' => [
                    [
                        'email' => 'test@company.com',
                        'phone' => ['097 876 76', '097 876 78', '097 876 77'],
                        'web' => 'company.com',
                    ],
                ],
                'nif' => '34557 7889 9800',
                'stat' => 'Madagascar',
            ],
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
