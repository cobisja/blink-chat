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
    final public const EMAIL_AVAILABILITY_URI = '/api/email/availability';

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
     * @dataProvider requestsContent
     */
    public function it_should_returns_a_non_ok_code_with_malformed_or_missing_parameters(
        string $content,
        int $expectedCode,
        array $violations
    ): void {
        $this->client->request(
            method: 'POST',
            uri: self::EMAIL_AVAILABILITY_URI,
            server: ['Content-Type' => 'application/json'],
            content: $content
        );

        $response = json_decode($this->client->getResponse()->getContent(), associative: true);

        $this->assertResponseStatusCodeSame($expectedCode);
        $this->assertArrayHasKey('error', $response);

        foreach ($violations as $index => $violation) {
            $this->assertArrayHasKey('propertyPath', $violation);
            $this->assertArrayHasKey('message', $violation);
            $this->assertSame($violation['propertyPath'], $response['error'][$index]['propertyPath']);
        }
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
            method: 'POST',
            uri: self::EMAIL_AVAILABILITY_URI,
            server: ['Content-Type' => 'application/json'],
            content: json_encode($userData)
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

    private function requestsContent(): array
    {
        return [

            'malformed_json_request' => [
                '{',
                Response::HTTP_BAD_REQUEST,
                [
                    [
                        'propertyPath' => null,
                        'message' => 'Syntax error'
                    ]
                ]
            ],

            'empty_request' => [
                '{}',
                Response::HTTP_UNPROCESSABLE_ENTITY,
                [
                    [
                        "propertyPath" => "email",
                        "message" => "This value should not be blank."
                    ],
                ]
            ],

            'empty_email' => [
                '{ "email": "" }',
                Response::HTTP_UNPROCESSABLE_ENTITY,
                [
                    [
                        "propertyPath" => "email",
                        "message" => "This value should not be blank."
                    ],
                ]
            ],

            'invalid_email' => [
                '{ "email": "test.test" }',
                Response::HTTP_UNPROCESSABLE_ENTITY,
                [
                    [
                        "propertyPath" => "email",
                        "message" => "This value is not a valid email address."
                    ],
                ]
            ],
        ];
    }

    private function availabilityContent(): array
    {
        return [
            ['test@test.test', true],
            ['test@test.test', false],
        ];
    }
}
