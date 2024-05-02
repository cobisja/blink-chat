<?php

declare(strict_types=1);

namespace App\Message\Auth;

final readonly class PasswordResetCreateMessage
{
    public function __construct(public string $email)
    {
    }
}