<?php

declare(strict_types=1);

namespace App\Service\Auth;

use App\Entity\User;
use App\Event\Auth\UserRegisteredEvent;
use App\Exception\Auth\EmailAlreadyTakenException;
use App\Exception\Auth\NicknameAlreadyTakenException;
use App\Exception\Auth\PasswordConfirmationDoesNotMatchException;
use App\Repository\UserRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

readonly class UserSignUpService
{
    public function __construct(
        private UserRepository $userRepository,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    /**
     * @throws NicknameAlreadyTakenException
     * @throws PasswordConfirmationDoesNotMatchException
     * @throws EmailAlreadyTakenException
     */
    public function __invoke(
        string $email,
        string $password,
        string $passwordConfirmation,
        string $name,
        string $lastname,
        string $nickname
    ): void {
        if ($this->userRepository->findByEmail($email)) {
            throw new EmailAlreadyTakenException();
        }

        if ($password !== $passwordConfirmation) {
            throw new PasswordConfirmationDoesNotMatchException();
        }

        if ($this->userRepository->findByNickname($nickname)) {
            throw new NicknameAlreadyTakenException();
        }

        $user = new User();

        $user->setEmail($email);
        $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
        $user->setName($name);
        $user->setLastname($lastname);
        $user->setNickname($nickname);

        $this->userRepository->save($user);

        $this->eventDispatcher->dispatch(
            new UserRegisteredEvent($user)
        );
    }
}
