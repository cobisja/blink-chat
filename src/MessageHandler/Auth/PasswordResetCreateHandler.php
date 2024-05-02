<?php

declare(strict_types=1);

namespace App\MessageHandler\Auth;

use App\Exception\Auth\ResetTokenCannotBeCreatedException;
use App\Message\Auth\PasswordResetCreateMessage;
use App\Service\Auth\PasswordResetCreateService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class PasswordResetCreateHandler
{
    public function __construct(private PasswordResetCreateService $passwordResetCreateService)
    {
    }

    /**
     * @throws ResetTokenCannotBeCreatedException
     */
    public function __invoke(PasswordResetCreateMessage $message): void
    {
        ($this->passwordResetCreateService)($message->email);
    }
}