<?php

namespace App\Message\Auth;

final readonly class UserSignUpMessage
{
    public function __construct(
        public string $email,
        public string $password,
        public string $passwordConfirmation,
        public string $name,
        public string $lastname,
        public string $nickname
    ) {
    }
}
