<?php

declare(strict_types=1);

namespace App\MessageHandler\Chat\User;

use App\Message\Chat\User\PaginatedUserIndexMessage;
use App\Service\Chat\User\PaginatedUserIndexService;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class PaginatedUserIndexHandler
{
    public function __construct(private PaginatedUserIndexService $paginatedUserIndexService)
    {
    }

    public function __invoke(PaginatedUserIndexMessage $message)
    {
        return ($this->paginatedUserIndexService)(
            query: $message->query,
            sort: $message->sort,
            page: $message->page,
            perPage: $message->perPage
        );
    }
}
