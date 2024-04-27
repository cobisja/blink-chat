<?php

declare(strict_types=1);

namespace App\Controller\Api\Auth\SignIn\Request;

use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

readonly class SignInCreateRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public ?string $email,

        #[Assert\NotBlank]
        public ?string $password,
    ) {
    }
}
