<?php

declare(strict_types=1);

namespace App\Service\Auth;

use App\Entity\PasswordReset;
use App\Event\Auth\PasswordResetCreatedEvent;
use App\Exception\Auth\ResetTokenCannotBeCreatedException;
use App\Repository\PasswordResetRepository;
use App\Repository\UserRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

readonly class PasswordResetCreateService
{
    public function __construct(
        private UserRepository $userRepository,
        private PasswordResetRepository $passwordResetRepository,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    /**
     * @throws ResetTokenCannotBeCreatedException
     */
    public function __invoke(string $email): void
    {
        if (!$this->userRepository->findByEmail($email)) {
            return;
        }

        if (!$passwordReset = $this->passwordResetRepository->findByEmail($email)) {
            $passwordReset = new PasswordReset();

            $passwordReset->setEmail($email);
        } else {
            $passwordReset->createToken();
        }

        $this->passwordResetRepository->save($passwordReset);

        $this->eventDispatcher->dispatch(
            new PasswordResetCreatedEvent($email)
        );
    }
}
