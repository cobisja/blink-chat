<?php

namespace App\Tests\Service\Auth;

use App\Entity\PasswordReset;
use App\Entity\User;
use App\Event\Auth\PasswordResetCreatedEvent;
use App\Exception\Auth\ResetTokenCannotBeCreatedException;
use App\Repository\PasswordResetRepository;
use App\Repository\UserRepository;
use App\Service\Auth\PasswordResetCreateService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PasswordResetCreateServiceTest extends TestCase
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
     * @throws ResetTokenCannotBeCreatedException
     */
    public function it_should_execute_the_create_password_reset_service_with_an_unregistered_email(): void
    {
        $email = 'unregistered@example.com';

        $this
            ->userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->willReturn(null);

        $this->eventDispatcher
            ->expects($this->never())
            ->method('dispatch')
            ->with($this->isInstanceOf(PasswordResetCreatedEvent::class));

        (new PasswordResetCreateService(
            $this->userRepository,
            $this->passwordResetRepository,
            $this->eventDispatcher
        ))(
            $email
        );
    }

    /**
     * @test
     * @throws ResetTokenCannotBeCreatedException
     */
    public function it_should_execute_the_create_password_reset_service_requesting_reset_token_for_first_time(): void
    {
        $email = 'registered@example.com';

        $this
            ->userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->willReturn(new User());

        $this
            ->passwordResetRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->willReturn(null);

        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(PasswordResetCreatedEvent::class));

        (new PasswordResetCreateService(
            $this->userRepository,
            $this->passwordResetRepository,
            $this->eventDispatcher
        ))(
            $email
        );
    }

    /**
     * @test
     * @throws ResetTokenCannotBeCreatedException
     */
    public function it_should_execute_the_create_password_reset_service_requesting_reset_token_for_again(): void
    {
        $email = 'registered@example.com';

        $this
            ->userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->willReturn(new User());

        $this
            ->passwordResetRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->willReturn(new PasswordReset());

        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(PasswordResetCreatedEvent::class));

        (new PasswordResetCreateService(
            $this->userRepository,
            $this->passwordResetRepository,
            $this->eventDispatcher
        ))(
            $email
        );
    }
}
