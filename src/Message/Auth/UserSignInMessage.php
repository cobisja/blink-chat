<?php

namespace App\Message\Auth;

final readonly class UserSignInMessage
{
    public function __construct(public string $email, public string $password)
    {
    }
}
