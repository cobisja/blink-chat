<?php

declare(strict_types=1);

namespace App\Controller\Api\Auth\Passwords\Request;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class PasswordsResetCreateRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public ?string $email
    ) {
    }
}
