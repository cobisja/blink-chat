<?php

namespace App\Tests\Controller\Api\Auth\Passwords;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PasswordsResetsCreateControllerTest extends WebTestCase
{
    final public const PASSWORDS_RESETS_URI = '/api/passwords_resets';

    private KernelBrowser $client;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $entityManager = self::getContainer()->get('doctrine')?->getManager();
        $this->userRepository = $entityManager->getRepository(User::class);
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
            uri: self::PASSWORDS_RESETS_URI,
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
     */
    public function it_should_returns_code_201_with_an_unregistered_email(): void
    {
        $email = ['email' => 'test@test.test'];
        $expectedCode = Response::HTTP_CREATED;

        $this->client->request(
            method: 'POST',
            uri: self::PASSWORDS_RESETS_URI,
            server: ['Content-Type' => 'application/json'],
            content: json_encode($email)
        );

        $response = json_decode($this->client->getResponse()->getContent(), associative: true);

        $this->assertResponseStatusCodeSame($expectedCode);
        $this->assertEmpty($response);
        $this->assertEmailCount(0);
    }

    /**
     * @test
     */
    public function it_should_returns_code_201_when_the_reset_token_is_requested(): void
    {
        $expectedCode = Response::HTTP_CREATED;
        $userData = [
            'email' => 'test@test.test',
            'password' => 'test-password',
            'name' => 'test',
            'lastname' => 'test',
            'nickname' => 'test-nickname'
        ];

        $this->createTestUser($userData);

        $this->client->request(
            method: 'POST',
            uri: self::PASSWORDS_RESETS_URI,
            server: ['Content-Type' => 'application/json'],
            content: json_encode(['email' => $userData['email']])
        );

        $response = json_decode($this->client->getResponse()->getContent(), associative: true);

        $this->assertResponseStatusCodeSame($expectedCode);
        $this->assertEmpty($response);
        $this->assertEmailCount(1);

        $email = $this->getMailerMessage();

        $this->assertEmailHtmlBodyContains($email, 'Reset password instructions');
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
                '{ "email": "invalid" }',
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
}
