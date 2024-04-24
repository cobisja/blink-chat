<?php

namespace App\MessageHandler\Auth;

use App\Exception\Auth\BadCredentialsException;
use App\Message\Auth\UserSignInMessage;
use App\Service\Auth\UserSignInService;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class UserSignInMessageHandler
{
    public function __construct(private UserSignInService $userSignInService)
    {
    }

    /**
     * @throws BadCredentialsException
     */
    #[ArrayShape([
        'token' => "string",
        'user' => "mixed"
    ])]
    public function __invoke(UserSignInMessage $message): array
    {
        return ($this->userSignInService)($message->email, $message->password);
    }
}
