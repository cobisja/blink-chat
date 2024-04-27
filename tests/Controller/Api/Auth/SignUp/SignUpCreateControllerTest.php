<?php

declare(strict_types=1);

namespace App\Tests\Controller\Api\Auth\SignUp;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SignUpCreateControllerTest extends WebTestCase
{
    final public const SIGN_UP_URI = '/api/sign-up';

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
        int $expectedCode,
        array $violations
    ): void {
        $this->client->request(
            method: 'POST',
            uri: self::SIGN_UP_URI,
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
    public function it_should_returns_code_422_with_an_email_already_taken(): void
    {
        $expectedErrorMessage = 'Email already taken';
        $expectedCode = Response::HTTP_UNPROCESSABLE_ENTITY;

        $userData = [
            'email' => 'test@test.test',
            'password' => 'test-password',
            'name' => 'test',
            'lastname' => 'test',
            'nickname' => 'test-nickname'
        ];

        $this->createTestUser($userData);
        $userData['password_confirmation'] = $userData['password'];

        $this->client->request(
            method: 'POST',
            uri: self::SIGN_UP_URI,
            server: ['Content-Type' => 'application/json'],
            content: json_encode($userData)
        );

        $response = json_decode($this->client->getResponse()->getContent(), associative: true);

        $this->assertResponseStatusCodeSame($expectedCode);
        $this->assertArrayHasKey('error', $response);
        $this->assertArrayHasKey('message', $response['error']);
        $this->assertEquals($expectedErrorMessage, $response['error']['message']);
    }

    /**
     * @test
     */
    public function it_should_returns_code_422_when_the_nickname_is_already_taken(): void
    {
        $expectedCode = Response::HTTP_UNPROCESSABLE_ENTITY;
        $expectedErrorMessage = 'Nickname already taken';

        $userData = [
            'email' => 'test@test.test',
            'password' => 'test-test',
            'name' => 'test',
            'lastname' => 'test',
            'nickname' => 'test-test'
        ];

        $this->createTestUser($userData);
        $userData['email'] = 'test-test@test.test';
        $userData['password_confirmation'] = $userData['password'];

        $this->client->request(
            method: 'POST',
            uri: self::SIGN_UP_URI,
            server: ['Content-Type' => 'application/json'],
            content: json_encode($userData)
        );

        $response = json_decode($this->client->getResponse()->getContent(), associative: true);

        $this->assertResponseStatusCodeSame($expectedCode);
        $this->assertArrayHasKey('error', $response);
        $this->assertArrayHasKey('message', $response['error']);
        $this->assertEquals($expectedErrorMessage, $response['error']['message']);
    }

    /**
     * @test
     */
    public function it_should_returns_code_201(): void
    {
        $expectedCode = Response::HTTP_CREATED;
        $expectedUserRoles = ['ROLE_USER'];

        $expectedUserData = [
            'email' => 'test@test.test',
            'password' => 'test-test',
            'name' => 'test',
            'lastname' => 'test',
            'nickname' => 'test-test'
        ];

        $expectedUserData['password_confirmation'] = $expectedUserData['password'];

        $this->client->request(
            method: 'POST',
            uri: self::SIGN_UP_URI,
            server: ['Content-Type' => 'application/json'],
            content: json_encode($expectedUserData)
        );

        $actualUserData = $this->userRepository->findByEmail($expectedUserData['email'])->data();
        $actualUserRoles = $actualUserData['roles'];

        unset(
            $expectedUserData['password'],
            $expectedUserData['password_confirmation'],
            $actualUserData['id'],
            $actualUserData['created_at'],
            $actualUserData['roles']
        );

        $this->assertResponseStatusCodeSame($expectedCode);
        $this->assertEquals($expectedUserData, $actualUserData);
        $this->assertEquals($expectedUserRoles, $actualUserRoles);
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
                    [
                        "propertyPath" => "password",
                        "message" => "This value should not be blank."
                    ],
                    [
                        "propertyPath" => "passwordConfirmation",
                        "message" => "This value should not be blank."
                    ],
                    [
                        "propertyPath" => "name",
                        "message" => "This value should not be blank."
                    ],
                    [
                        "propertyPath" => "lastname",
                        "message" => "This value should not be blank."
                    ],
                    [
                        "propertyPath" => "nickname",
                        "message" => "This value should not be blank."
                    ],
                ]
            ],

            'missing_email' => [
                <<<JSON
                {
                    "password": "test-test",
                    "password_confirmation": "test-test",
                    "name": "test",
                    "lastname": "test",
                    "nickname": "test-test"
                }
                JSON,
                Response::HTTP_UNPROCESSABLE_ENTITY,
                [
                    [
                        'propertyPath' => 'email',
                        'message' => 'This value should not be blank.'
                    ]
                ],
            ],

            'missing_password' => [
                <<<JSON
                {
                    "email": "test@test.test",
                    "password_confirmation": "test-test",
                    "name": "test",
                    "lastname": "test",
                    "nickname": "test-test"
                }
                JSON,
                Response::HTTP_UNPROCESSABLE_ENTITY,
                [
                    [
                        'propertyPath' => 'password',
                        'message' => 'This value should not be blank.'
                    ],
                    [
                        'propertyPath' => 'passwordConfirmation',
                        'message' => 'Password confirmation does not match.'
                    ]
                ],
            ],

            'short_password' => [
                <<<JSON
                {
                    "email": "test@test.test",
                    "password": "test",
                    "password_confirmation": "test",
                    "name": "test",
                    "lastname": "test",
                    "nickname": "test-test"
                }
                JSON,
                Response::HTTP_UNPROCESSABLE_ENTITY,
                [
                    [
                        'propertyPath' => 'password',
                        'message' => 'This value is too short. It should have 6 characters or more.'
                    ]
                ],
            ],

            'missing_password_confirmation' => [
                <<<JSON
                {
                    "email": "test@test.test",
                    "password": "test-test",
                    "name": "test",
                    "lastname": "test",
                    "nickname": "test-test"
                }
                JSON,
                Response::HTTP_UNPROCESSABLE_ENTITY,
                [
                    [
                        'propertyPath' => 'passwordConfirmation',
                        'message' => 'This value should not be blank.'
                    ]
                ],
            ],

            'mismatch_password_confirmation' => [
                <<<JSON
                {
                    "email": "test@test.test",
                    "password": "test-test",
                    "password_confirmation": "test-test-test",
                    "name": "test",
                    "lastname": "test",
                    "nickname": "test-test"
                }
                JSON,
                Response::HTTP_UNPROCESSABLE_ENTITY,
                [
                    [
                        'propertyPath' => 'passwordConfirmation',
                        'message' => 'Password confirmation does not match.'
                    ]
                ],
            ],

            'missing_name' => [
                <<<JSON
                {
                    "email": "test@test.test",
                    "password": "test-test",
                    "password_confirmation": "test-test",
                    "lastname": "test",
                    "nickname": "test-test"
                }
                JSON,
                Response::HTTP_UNPROCESSABLE_ENTITY,
                [
                    [
                        'propertyPath' => 'name',
                        'message' => 'This value should not be blank.'
                    ]
                ],
            ],

            'missing_lastname' => [
                <<<JSON
                {
                    "email": "test@test.test",
                    "password": "test-test",
                    "password_confirmation": "test-test",
                    "name": "test",
                    "nickname": "test-test"
                }
                JSON,
                Response::HTTP_UNPROCESSABLE_ENTITY,
                [
                    [
                        'propertyPath' => 'lastname',
                        'message' => 'This value should not be blank.'
                    ]
                ],
            ],

            'missing_nickname' => [
                <<<JSON
                {
                    "email": "test@test.test",
                    "password": "test-test",
                    "password_confirmation": "test-test",
                    "name": "test",
                    "lastname": "test"
                }
                JSON,
                Response::HTTP_UNPROCESSABLE_ENTITY,
                [
                    [
                        'propertyPath' => 'nickname',
                        'message' => 'This value should not be blank.'
                    ]
                ],
            ],

            'short_nickname' => [
                <<<JSON
                {
                    "email": "test@test.test",
                    "password": "test-test",
                    "password_confirmation": "test-test",
                    "name": "test",
                    "lastname": "test"
                }
                JSON,
                Response::HTTP_UNPROCESSABLE_ENTITY,
                [
                    [
                        'propertyPath' => 'nickname',
                        'message' => 'This value is too short. It should have 5 characters or more.'
                    ]
                ]
            ]
        ];
    }
}
