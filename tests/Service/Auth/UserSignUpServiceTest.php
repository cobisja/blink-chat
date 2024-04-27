<?php

namespace App\Tests\Service\Auth;

use App\Entity\User;
use App\Event\Auth\UserRegisteredEvent;
use App\Exception\Auth\EmailAlreadyTakenException;
use App\Exception\Auth\NicknameAlreadyTakenException;
use App\Exception\Auth\PasswordConfirmationDoesNotMatchException;
use App\Repository\UserRepository;
use App\Service\Auth\UserSignUpService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UserSignUpServiceTest extends TestCase
{
    private UserRepository $userRepository;
    private EventDispatcherInterface $eventDispatcher;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
    }

    /**
     * @test
     * @throws NicknameAlreadyTakenException
     * @throws PasswordConfirmationDoesNotMatchException
     */
    public function it_should_triggers_email_already_taken_exception_with_a_taken_email(): void
    {
        $this->expectException(EmailAlreadyTakenException::class);

        $userData = ['test@test.test', 'password', 'password', 'test', 'test', 'test',];

        $email = $userData[0];
        $user = new User();

        $user->setEmail($email);

        $this->userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->with($email)
            ->willReturn($user);

        (new UserSignUpService($this->userRepository, $this->eventDispatcher))(
            ...$userData
        );
    }

    /**
     * @test
     * @throws NicknameAlreadyTakenException
     * @throws EmailAlreadyTakenException
     */
    public function it_should_triggers_password_confirmation_does_not_match_exception_with_mismatch_passwords(): void
    {
        $this->expectException(PasswordConfirmationDoesNotMatchException::class);

        $userData = ['test@test.test', 'password', 'not-matching-password', 'test', 'test', 'test',];

        $this->userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->with($userData[0])
            ->willReturn(null);

        (new UserSignUpService($this->userRepository, $this->eventDispatcher))(
            ...$userData
        );
    }

    /**
     * @test
     * @throws PasswordConfirmationDoesNotMatchException
     * @throws EmailAlreadyTakenException
     */
    public function it_should_triggers_nickname_already_taken_exception_with_a_taken_nickname(): void
    {
        $this->expectException(NicknameAlreadyTakenException::class);

        $userData = ['test@test.test', 'password', 'password', 'test', 'test', 'test'];

        $email = $userData[0];
        $nickname = $userData[5];
        $user = new User();

        $user->setEmail($nickname);
        $user->setEmail($email);

        $this->userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->with($email)
            ->willReturn(null);

        $this->userRepository
            ->expects($this->once())
            ->method('findByNickname')
            ->with($nickname)
            ->willReturn($user);

        (new UserSignUpService($this->userRepository, $this->eventDispatcher))(
            ...$userData
        );
    }

    /**
     * @test
     * @throws NicknameAlreadyTakenException
     * @throws EmailAlreadyTakenException
     * @throws PasswordConfirmationDoesNotMatchException
     */
    public function it_should_register_the_user_and_dispatch_user_registered_event(): void
    {
        $userData = ['test@test.test', 'password', 'password', 'test', 'test', 'test'];

        $email = $userData[0];
        $nickname = $userData[5];
        $user = new User();

        $user->setEmail($nickname);
        $user->setEmail($email);

        $this->userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->with($email)
            ->willReturn(null);

        $this->userRepository
            ->expects($this->once())
            ->method('findByNickname')
            ->with($nickname)
            ->willReturn(null);

        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(UserRegisteredEvent::class));

        (new UserSignUpService($this->userRepository, $this->eventDispatcher))(
            ...$userData
        );
    }
}
