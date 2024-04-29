<?php

declare(strict_types=1);

namespace App\Service\Shared;

use App\Repository\UserRepository;

readonly class EmailAvailabilityShowService
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function __invoke(string $email): bool
    {
        return !$this->userRepository->findByEmail($email);
    }
}