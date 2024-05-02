<?php

namespace App\Tests\Controller\Api\Auth\Passwords;

use App\Entity\PasswordReset;
use App\Entity\User;
use App\Repository\PasswordResetRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PasswordsResetsUpdateControllerTest extends WebTestCase
{
    final public const PASSWORDS_RESETS_URI = '/api/passwords_resets';

    private KernelBrowser $client;
    private PasswordResetRepository $passwordResetRepository;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::getContainer()->get('doctrine')?->getManager();

        $this->userRepository = $entityManager->getRepository(User::class);
        $this->passwordResetRepository = $entityManager->getRepository(PasswordReset::class);
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
        $token = "test";

        $this->client->request(
            method: 'POST',
            uri: sprintf('%s/%s', self::PASSWORDS_RESETS_URI, $token),
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
    public function it_should_returns_code_422_with_unknown_token(): void
    {
        $expectedCode = Response::HTTP_UNPROCESSABLE_ENTITY;
        $expectedPropertyPath = 'token';
        $expectedErrorMessage = 'Reset token not found';
        $token = "test";
        $password = "test-test";

        $this->client->request(
            method: 'POST',
            uri: sprintf('%s/%s', self::PASSWORDS_RESETS_URI, $token),
            server: ['Content-Type' => 'application/json'],
            content: json_encode(['password' => $password])
        );

        $response = json_decode($this->client->getResponse()->getContent(), associative: true);

        $this->assertResponseStatusCodeSame($expectedCode);
        $this->assertArrayHasKey('error', $response);
        $this->assertArrayHasKey('propertyPath', $response['error'][0]);
        $this->assertArrayHasKey('message', $response['error'][0]);
        $this->assertSame($expectedPropertyPath, $response['error'][0]['propertyPath']);
        $this->assertSame($expectedErrorMessage, $response['error'][0]['message']);
    }

    /**
     * @test
     */
    public function it_should_returns_code_422_with_expired_token(): void
    {
        $expectedCode = Response::HTTP_UNPROCESSABLE_ENTITY;
        $expectedPropertyPath = 'token';
        $expectedErrorMessage = 'Reset token expired';
        $password = "test-test";
        $email = "test@test.test";

        $passwordReset = new PasswordReset();
        $passwordReset->setEmail($email);
        $passwordReset->setValidUntil(new DateTimeImmutable("-1 day"));

        $this->passwordResetRepository->save($passwordReset);

        $this->client->request(
            method: 'POST',
            uri: sprintf('%s/%s', self::PASSWORDS_RESETS_URI, $passwordReset->getToken()),
            server: ['Content-Type' => 'application/json'],
            content: json_encode(['password' => $password])
        );

        $response = json_decode($this->client->getResponse()->getContent(), associative: true);

        $this->assertResponseStatusCodeSame($expectedCode);
        $this->assertArrayHasKey('error', $response);
        $this->assertArrayHasKey('propertyPath', $response['error'][0]);
        $this->assertArrayHasKey('message', $response['error'][0]);
        $this->assertSame($expectedPropertyPath, $response['error'][0]['propertyPath']);
        $this->assertSame($expectedErrorMessage, $response['error'][0]['message']);
    }

    /**
     * @test
     */
    public function it_should_resets_the_password(): void
    {
        $expectedCode = Response::HTTP_NO_CONTENT;
        $newPassword = "test-test-test";

        $userData = [
            'email' => 'test@test.test',
            'password' => 'test-test',
            'name' => 'test',
            'lastname' => 'test',
            'nickname' => 'test-test'
        ];

        $user = new User();
        $user->setEmail($userData['email']);
        $user->setPassword(password_hash($userData['password'], PASSWORD_DEFAULT));
        $user->setName($userData['name']);
        $user->setLastname($userData['lastname']);
        $user->setNickname($userData['nickname']);

        $this->userRepository->save($user);

        $passwordReset = new PasswordReset();
        $passwordReset->setEmail($user->getEmail());

        $this->passwordResetRepository->save($passwordReset);

        $this->client->request(
            method: 'POST',
            uri: sprintf('%s/%s', self::PASSWORDS_RESETS_URI, $passwordReset->getToken()),
            server: ['Content-Type' => 'application/json'],
            content: json_encode(['password' => $newPassword])
        );

        $response = json_decode($this->client->getResponse()->getContent(), associative: true);

        $this->assertResponseStatusCodeSame($expectedCode);
        $this->assertNull(
            $this->passwordResetRepository->findByToken($passwordReset->getToken())
        );
        $this->assertFalse(
            password_verify($userData['password'], $user->getPassword())
        );
        $this->assertTrue(
            password_verify($newPassword, $user->getPassword())
        );

        $this->assertEmailCount(1);

        $email = $this->getMailerMessage();

        $this->assertEmailHtmlBodyContains($email, 'Your password has changed');
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
                        "propertyPath" => "password",
                        "message" => "This value should not be blank."
                    ],
                ]
            ],

            'empty_password' => [
                '{ "password": "" }',
                Response::HTTP_UNPROCESSABLE_ENTITY,
                [
                    [
                        "propertyPath" => "password",
                        "message" => "This value should not be blank."
                    ],
                ]
            ],

            'short_password' => [
                '{ "password": "test" }',
                Response::HTTP_UNPROCESSABLE_ENTITY,
                [
                    [
                        "propertyPath" => "password",
                        "message" => "This value is not a valid email address."
                    ],
                ]
            ],
        ];
    }
}
