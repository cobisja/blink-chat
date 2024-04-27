<?php

declare(strict_types=1);

namespace App\Controller\Api\Auth\SignUp\Request;

use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\PasswordStrength;

readonly class SignUpCreateRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        #[Assert\Length(max: 180)]
        public ?string $email,

        #[Assert\NotBlank]
        #[Assert\Length(min: User::PASSWORD_MIN_LENGTH)]
        public ?string $password,

        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        #[Assert\EqualTo(propertyPath: 'password', message: 'Password confirmation does not match.')]
        public ?string $passwordConfirmation,

        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public ?string $name,

        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public ?string $lastname,

        #[Assert\NotBlank]
        #[Assert\Length(min: 5, max: 16)]
        public ?string $nickname
    ) {
    }
}
