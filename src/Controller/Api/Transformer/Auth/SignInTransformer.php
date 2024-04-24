<?php

declare(strict_types=1);

namespace App\Controller\Api\Transformer\Auth;

use App\Controller\Api\Transformer\User\UserTransformer;

readonly class SignInTransformer
{
    public function __construct(private UserTransformer $userTransformer)
    {
    }

    public function transform(array $authData): array
    {
        return [
            'token' => $authData['token'],
            'user' => $this->userTransformer->transform($authData['user']),
        ];
    }
}
