<?php

namespace App\MessageHandler\Auth;

use App\Exception\Auth\EmailAlreadyTakenException;
use App\Exception\Auth\NicknameAlreadyTakenException;
use App\Exception\Auth\PasswordConfirmationDoesNotMatchException;
use App\Message\Auth\UserSignUpMessage;
use App\Service\Auth\UserSignUpService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class UserSignUpMessageHandler
{
    public function __construct(private UserSignUpService $userSignUpServiceService)
    {
    }

    /**
     * @throws NicknameAlreadyTakenException
     * @throws EmailAlreadyTakenException
     * @throws PasswordConfirmationDoesNotMatchException
     */
    public function __invoke(UserSignUpMessage $message): void
    {
        ($this->userSignUpServiceService)(
            $message->email,
            $message->password,
            $message->passwordConfirmation,
            $message->name,
            $message->lastname,
            $message->nickname
        );
    }
}
