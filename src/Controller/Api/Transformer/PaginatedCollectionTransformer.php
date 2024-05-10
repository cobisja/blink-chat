<?php

declare(strict_types=1);

namespace App\Controller\Api\Transformer;

use App\Service\Paginator\PaginatorServiceInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

readonly class PaginatedCollectionTransformer
{
    public function __construct(private UrlGeneratorInterface $urlGenerator, private RequestStack $requestStack)
    {
    }

    public function transform(PaginatorServiceInterface $paginator, string $route): array
    {
        $routeParams = $this->requestStack->getCurrentRequest()->query->all();
        $urlLink = function ($target) use ($route, $routeParams) {
            return $this->urlGenerator->generate(
                $route,
                array_merge(
                    $routeParams,
                    ['page' => $target]
                ),
                UrlGeneratorInterface::ABSOLUTE_URL
            );
        };

        $results = array_map(static fn($item) => $item, iterator_to_array($paginator->getCurrentPageResults()));

        $paginatedCollection = [
            'data' => $results,
            'meta' => [
                '_links' => [
                    'self' => $urlLink($routeParams['page'] ?? 1),
                    'first' => $urlLink(1),
                    'last' => $urlLink($paginator->getNumberOfResults()),
                ],
                'total' => $paginator->getNumberOfResults(),
                'count' => count($results),
            ]
        ];

        if ($paginator->hasNextPage()) {
            $paginatedCollection['meta']['_links']['next'] = $urlLink($paginator->getNextPage());
        }

        if ($paginator->hasPreviousPage()) {
            $paginatedCollection['meta']['_links']['prev'] = $urlLink($paginator->getPreviousPage());
        }

        return $paginatedCollection;
    }
}