<?php

namespace App\Tests\Service\Auth;

use App\Entity\PasswordReset;
use App\Entity\User;
use App\Event\Auth\UserPasswordUpdatedEvent;
use App\Exception\Auth\PasswordNotValidException;
use App\Exception\Auth\PasswordResetNotFound;
use App\Exception\Auth\ResetTokenExpiredException;
use App\Repository\PasswordResetRepository;
use App\Repository\UserRepository;
use App\Service\Auth\PasswordResetUpdateService;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\Comparator\Comparator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PasswordResetUpdateServiceTest extends TestCase
{
    private UserRepository $userRepository;
    private PasswordResetRepository $passwordResetRepository;
    private EventDispatcherInterface $eventDispatcher;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->passwordResetRepository = $this->createMock(PasswordResetRepository::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
    }

    /**
     * @test
     * @throws PasswordNotValidException
     * @throws ResetTokenExpiredException
     * @throws PasswordResetNotFound
     */
    public function it_should_triggers_password_not_valid_exception_with_short_password(): void
    {
        $this->expectException(PasswordNotValidException::class);

        $token = "test";
        $password = "test";

        (new PasswordResetUpdateService($this->userRepository, $this->passwordResetRepository, $this->eventDispatcher))(
            $token,
            $password
        );
    }

    /**
     * @test
     * @throws PasswordNotValidException
     * @throws ResetTokenExpiredException
     */
    public function it_should_triggers_password_reset_not_found_exception_with_unknown_token(): void
    {
        $this->expectException(PasswordResetNotFound::class);

        $token = "test";
        $password = "test-test";

        $this
            ->passwordResetRepository
            ->expects($this->once())
            ->method('findByToken')
            ->willReturn(null);

        (new PasswordResetUpdateService($this->userRepository, $this->passwordResetRepository, $this->eventDispatcher))(
            $token,
            $password
        );
    }

    /**
     * @test
     * @throws PasswordNotValidException
     * @throws PasswordResetNotFound
     */
    public function it_should_triggers_reset_token_expired_exception_with_expired_token(): void
    {
        $this->expectException(ResetTokenExpiredException::class);

        $token = "test";
        $password = "test-test";

        $passwordReset = new PasswordReset();

        $passwordReset->setValidUntil(new DateTimeImmutable("-1 day"));

        $this
            ->passwordResetRepository
            ->expects($this->once())
            ->method('findByToken')
            ->willReturn($passwordReset);

        (new PasswordResetUpdateService($this->userRepository, $this->passwordResetRepository, $this->eventDispatcher))(
            $token,
            $password
        );
    }

    /**
     * @test
     * @throws PasswordNotValidException
     * @throws PasswordResetNotFound
     * @throws ResetTokenExpiredException
     */
    public function it_should_triggers_password_reset_not_found_exception_with_unknown_user(): void
    {
        $this->expectException(PasswordResetNotFound::class);

        $token = "test";
        $password = "test-test";
        $email = "test@test.test";

        $passwordReset = new PasswordReset();

        $passwordReset->setEmail($email);

        $this
            ->passwordResetRepository
            ->expects($this->once())
            ->method('findByToken')
            ->willReturn($passwordReset);

        $this
            ->userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->willReturn(null);

        (new PasswordResetUpdateService($this->userRepository, $this->passwordResetRepository, $this->eventDispatcher))(
            $token,
            $password
        );
    }

    /**
     * @test
     * @throws PasswordNotValidException
     * @throws ResetTokenExpiredException
     * @throws PasswordResetNotFound
     */
    public function it_should_executes_with_no_exceptions(): void
    {
        $token = "test";
        $password = "test-test";
        $email = "test@test.test";

        $passwordReset = new PasswordReset();

        $passwordReset->setEmail($email);

        $this
            ->passwordResetRepository
            ->expects($this->once())
            ->method('findByToken')
            ->willReturn($passwordReset);

        $user = new User();

        $this
            ->userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->willReturn($user);

        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(UserPasswordUpdatedEvent::class));

        (new PasswordResetUpdateService($this->userRepository, $this->passwordResetRepository, $this->eventDispatcher))(
            $token,
            $password
        );
    }
}
