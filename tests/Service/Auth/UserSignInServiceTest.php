<?php

declare(strict_types=1);

namespace App\Tests\Service\Auth;

use App\Entity\User;
use App\Exception\Auth\BadCredentialsException;
use App\Repository\UserRepository;
use App\Service\Auth\UserSignInService;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PHPUnit\Framework\TestCase;

class UserSignInServiceTest extends TestCase
{
    private UserRepository $userRepository;
    private JWTTokenManagerInterface $JWTManager;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->JWTManager = $this->createMock(JWTManager::class);
    }

    /**
     * @test
     * @throws BadCredentialsException
     */
    public function it_should_triggers_bad_credentials_exception_with_wrong_email(): void
    {
        $this->expectException(BadCredentialsException::class);

        $email = 'test@test.test';
        $password = 'test';

        $this->userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->willReturn(null);

        (new UserSignInService($this->userRepository, $this->JWTManager))(
            $email,
            $password
        );
    }

    /**
     * @test
     * @throws BadCredentialsException
     */
    public function it_should_triggers_bad_credentials_exception_with_wrong_password(): void
    {
        $this->expectException(BadCredentialsException::class);

        $email = 'test@test.test';
        $correctPassword = password_hash('correct-password', PASSWORD_DEFAULT);
        $incorrectPassword = 'incorrect-password';

        $user = new User();

        $user->setEmail($email);
        $user->setPassword($correctPassword);

        $this->userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->willReturn($user);

        (new UserSignInService($this->userRepository, $this->JWTManager))(
            $email,
            $incorrectPassword
        );
    }

    /**
     * @test
     * @throws BadCredentialsException
     */
    public function it_should_returns_auth_data(): void
    {
        $email = 'test@test.test';
        $password = 'test';
        $token = 'jwtToken';

        $user = new User();

        $user->setEmail($email);
        $user->setPassword(password_hash($password, PASSWORD_DEFAULT));

        $this->userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->willReturn($user);

        $this->JWTManager
            ->expects($this->once())
            ->method('createFromPayload')
            ->willReturn($token);

        $authData = (new UserSignInService($this->userRepository, $this->JWTManager))(
            $email,
            $password
        );

        $this->assertNotEmpty($authData);
        $this->assertArrayHasKey('token', $authData);
        $this->assertArrayHasKey('user', $authData);
        $this->assertIsString($authData['token']);
        $this->assertInstanceOf(User::class, $authData['user']);
    }
}
