<?php

declare(strict_types=1);

namespace App\Message\Auth;

final readonly class PasswordResetUpdateMessage
{
    public function __construct(public string $token, public string $password)
    {
    }
}