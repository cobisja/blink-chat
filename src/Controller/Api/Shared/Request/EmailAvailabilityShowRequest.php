<?php

declare(strict_types=1);

namespace App\Controller\Api\Shared\Request;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class EmailAvailabilityShowRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public ?string $email,
    ) {
    }
}