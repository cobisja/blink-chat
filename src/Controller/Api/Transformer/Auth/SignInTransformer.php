<?php

declare(strict_types=1);

namespace App\Controller\Api\Transformer\Auth;

use App\Controller\Api\Transformer\User\UserTransformer;
use JetBrains\PhpStorm\ArrayShape;

readonly class SignInTransformer
{
    public function __construct(private UserTransformer $userTransformer)
    {
    }

    #[ArrayShape([
        'token' => "string",
        'user' => "array"
    ])]
    public function transform(array $authData): array
    {
        return [
            'token' => $authData['token'],
            'user' => $this->userTransformer->transform($authData['user']),
        ];
    }
}
