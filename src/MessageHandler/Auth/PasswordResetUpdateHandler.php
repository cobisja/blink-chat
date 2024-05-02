<?php

declare(strict_types=1);

namespace App\MessageHandler\Auth;

use App\Exception\Auth\PasswordNotValidException;
use App\Exception\Auth\PasswordResetNotFound;
use App\Exception\Auth\ResetTokenExpiredException;
use App\Message\Auth\PasswordResetUpdateMessage;
use App\Service\Auth\PasswordResetUpdateService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class PasswordResetUpdateHandler
{
    public function __construct(private PasswordResetUpdateService $passwordResetUpdateService)
    {
    }

    /**
     * @throws PasswordNotValidException
     * @throws ResetTokenExpiredException
     * @throws PasswordResetNotFound
     */
    public function __invoke(PasswordResetUpdateMessage $message): void
    {
        ($this->passwordResetUpdateService)($message->token, $message->password);
    }
}
