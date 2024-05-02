<?php

declare(strict_types=1);

namespace App\Service\Auth;

use App\Entity\User;
use App\Event\Auth\UserPasswordUpdatedEvent;
use App\Exception\Auth\PasswordNotValidException;
use App\Exception\Auth\PasswordResetNotFound;
use App\Exception\Auth\ResetTokenExpiredException;
use App\Repository\PasswordResetRepository;
use App\Repository\UserRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

readonly class PasswordResetUpdateService
{
    public function __construct(
        private UserRepository $userRepository,
        private PasswordResetRepository $passwordResetRepository,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    /**
     * @throws PasswordNotValidException
     * @throws ResetTokenExpiredException
     * @throws PasswordResetNotFound
     */
    public function __invoke(string $token, string $password): void
    {
        $password = trim($password);

        if (User::PASSWORD_MIN_LENGTH > strlen($password)) {
            throw new PasswordNotValidException(
                sprintf('Password needs to be at least %d chars', User::PASSWORD_MIN_LENGTH)
            );
        }

        if (!$passwordReset = $this->passwordResetRepository->findByToken($token)) {
            throw new PasswordResetNotFound('Reset token not found');
        }

        if ($passwordReset->isExpired()) {
            throw new ResetTokenExpiredException();
        }

        $userEmail = $passwordReset->getEmail();

        if (!$user = $this->userRepository->findByEmail($userEmail)) {
            throw new PasswordResetNotFound('Invalid reset token');
        }

        $user->setPassword(password_hash($password, PASSWORD_DEFAULT));

        $this->passwordResetRepository->remove($passwordReset);
        $this->userRepository->save($user);

        $this->eventDispatcher->dispatch(
            new UserPasswordUpdatedEvent($userEmail)
        );
    }
}