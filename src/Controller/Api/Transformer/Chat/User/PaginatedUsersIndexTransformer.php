<?php

declare(strict_types=1);

namespace App\Controller\Api\Transformer\Chat\User;

use App\Controller\Api\Transformer\PaginatedCollectionTransformer;
use App\Controller\Api\Transformer\User\UserTransformer;
use App\Entity\User;

final readonly class PaginatedUsersIndexTransformer
{
    public function __construct(
        private PaginatedCollectionTransformer $paginatedCollectionTransformer,
        private UserTransformer $userTransformer,
    ) {
    }

    public function transform($result, string $route): array
    {
        $results = $this->paginatedCollectionTransformer->transform($result, $route);

        $results['data'] = array_map(
            fn(User $user) => $this->userTransformer->transform($user),
            $results['data']
        );

        return $results;
    }
}
