<?php

declare(strict_types=1);

namespace App\Tests\Controller\Api\Auth\SignIn;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SignInCreateControllerTest extends WebTestCase
{
    final public const SIGN_IN_URI = '/api/sign-in';

    private ?ObjectManager $entityManager;
    private KernelBrowser $client;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = self::getContainer()->get('doctrine')?->getManager();
        $this->userRepository = $this->entityManager->getRepository(User::class);
    }

    /**
     * @test
     * @dataProvider requestsContent
     */
    public function it_should_returns_a_non_ok_code_with_malformed_or_missing_parameters(
        string $content,
        int $expectedCode
    ): void {
        $this->client->request(
            method: 'POST',
            uri: self::SIGN_IN_URI,
            server: ['Content-Type' => 'application/json'],
            content: $content
        );

        $this->assertResponseStatusCodeSame($expectedCode);
    }

    /**
     * @test
     */
    public function it_should_returns_code_401_with_bad_credentials(): void
    {
        $userData = [
            'email' => 'test@test.test',
            'password' => 'test-test',
            'name' => 'test',
            'lastname' => 'test',
            'nickname' => 'test-test'
        ];

        $this->createTestUser($userData);

        $content = ['email' => $userData['email'], 'password' => $userData['password'] . '*--test--*'];

        $this->client->request(
            method: 'POST',
            uri: self::SIGN_IN_URI,
            server: ['Content-Type' => 'application/json'],
            content: json_encode($content)
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     * @dataProvider invalidContent
     */
    public function it_should_returns_code_422_and_expected_response_structure_with_invalid_data(
        string $content,
        int $errorCount,
        array $propertyPath
    ): void {
        $this->client->request(
            method: 'POST',
            uri: self::SIGN_IN_URI,
            server: ['Content-Type' => 'application/json'],
            content: $content
        );

        $response = json_decode($this->client->getResponse()->getContent(), associative: true);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertArrayHasKey('error', $response);
        $this->assertCount($errorCount, $response['error']);

        foreach (range(0, $errorCount - 1) as $index) {
            $this->assertTrue(in_array('propertyPath', array_keys($response['error'][$index])));
            $this->assertTrue(in_array('message', array_keys($response['error'][$index])));
            $this->assertSame($propertyPath[$index], $response['error'][$index]['propertyPath']);
        }
    }

    /**
     * @test
     */
    public function it_should_returns_code_200_with_auth_data_in_the_response(): void
    {
        $userData = [
            'email' => 'test@test.test',
            'password' => 'test-test',
            'name' => 'test',
            'lastname' => 'test',
            'nickname' => 'test-test'
        ];

        $this->createTestUser($userData);

        $this->client->request(
            method: 'POST',
            uri: self::SIGN_IN_URI,
            server: ['Content-Type' => 'application/json'],
            content: json_encode(['email' => $userData['email'], 'password' => $userData['password']])
        );

        $expectedUser = $this->userRepository->findByEmail($userData['email']);
        $response = json_decode($this->client->getResponse()->getContent(), associative: true);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('token', $response['data']);
        $this->assertArrayHasKey('user', $response['data']);
        $this->assertNotEmpty($response['data']['token']);
        $this->assertSame((string)$expectedUser->getId(), $response['data']['user']['id']);
        $this->assertSame($expectedUser->getEmail(), $response['data']['user']['email']);
        $this->assertSame($expectedUser->getName(), $response['data']['user']['name']);
        $this->assertSame($expectedUser->getLastname(), $response['data']['user']['lastname']);
        $this->assertSame($expectedUser->getNickname(), $response['data']['user']['nickname']);
        $this->assertSame($expectedUser->getRoles(), $response['data']['user']['roles']);
        $this->assertSame($expectedUser->getCreatedAt()->getTimestamp(), $response['data']['user']['created_at']);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
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

    private function requestsContent(): array
    {
        return [
            'malformed_json_request' => ['{', Response::HTTP_BAD_REQUEST],
            'empty_request' => ['{}', Response::HTTP_UNPROCESSABLE_ENTITY],
            'missing_email' => [
                '{"password": "*unbreakable-password*"}',
                Response::HTTP_UNPROCESSABLE_ENTITY
            ],
            'missing_password' => [
                '{"email": "jane.doe.example.org"}',
                Response::HTTP_UNPROCESSABLE_ENTITY
            ],
        ];
    }

    private function invalidContent(): array
    {
        return [
            'invalid_email' => [
                '{"email": "abc", "password": "123"}',
                1,
                ['email', 'password']
            ]
        ];
    }
}