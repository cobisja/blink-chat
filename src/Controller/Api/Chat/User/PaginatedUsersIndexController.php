<?php

declare(strict_types=1);

namespace App\Controller\Api\Chat\User;

use App\Controller\Api\ApiController;
use App\Controller\Api\ApiResponse;
use App\Controller\Api\Transformer\Chat\User\PaginatedUsersIndexTransformer;
use App\Message\Chat\User\PaginatedUserIndexMessage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class PaginatedUsersIndexController extends ApiController
{
    #[Route('/users', name: 'api_chat_paginated_users_index', methods: ['GET'])]
    public function __invoke(Request $request, PaginatedUsersIndexTransformer $paginatedUsersIndexTransformer): ApiResponse {
        $users = $this->query(
            new PaginatedUserIndexMessage(
                query: $request->query->get('query'),
                sort: $request->query->get('direction', 'asc'),
                page: $request->query->getInt('page', 1),
                perPage: $request->query->getInt('limit', 10),
            )
        );

        return ApiResponse::ok(
            $paginatedUsersIndexTransformer->transform(result: $users, route: 'api_chat_paginated_users_index')
        );
    }
}
