<?php

namespace App\Tests\Controller\Api\Shared;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class EmailAvailabilityShowControllerTest extends WebTestCase
{
    final public const EMAIL_AVAILABILITY_URI = '/api/emails/availability';

    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = self::getContainer()->get('doctrine')?->getManager();
        $this->userRepository = $this->entityManager->getRepository(User::class);
    }

    /**
     * @test
     * @dataProvider availabilityContent
     */
    public function it_should_returns_a_200_code_indicating_the_email_availability(
        string $email,
        bool $isAvailable
    ): void {
        $userData = [
            'email' => $isAvailable ? 'john.doe@example.org' : $email,
            'password' => 'test-password',
            'name' => 'test',
            'lastname' => 'test',
            'nickname' => 'test-nickname'
        ];

        $this->createTestUser($userData);

        $userData['email'] = $email;

        $this->client->request(
            method: 'GET',
            uri: self::EMAIL_AVAILABILITY_URI,
            parameters: ['email' => $userData['email']]
        );

        $response = json_decode($this->client->getResponse()->getContent(), associative: true);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('available', $response['data']);
        $this->assertIsBool($response['data']['available']);
        $this->assertEquals($isAvailable, $response['data']['available']);
    }

    private function createTestUser(array $userData): void
    {
        $user = new User();

        $user->setEmail($userData['email']);
        $user->setPassword(password_hash($userData['password'], PASSWORD_DEFAULT));
        $user->setName($userData['name']);
        $user->setLastName($userData['lastname']);
        $user->setNickname($userData['nickname']);

        $this->userRepository->save($user);
    }

    private function availabilityContent(): array
    {
        return [
            ['test@test.test', true],
            ['test@test.test', false],
        ];
    }
}
