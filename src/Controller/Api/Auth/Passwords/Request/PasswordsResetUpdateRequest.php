<?php

declare(strict_types=1);

namespace App\Controller\Api\Auth\Passwords\Request;

use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class PasswordsResetUpdateRequest
{
    public function __construct(
        #[Assert\NotBlank]
        public ?string $token,

        #[Assert\NotBlank]
        #[Assert\Length(min: User::PASSWORD_MIN_LENGTH)]
        public ?string $password,
    ) {
    }
}
