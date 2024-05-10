<?php

declare(strict_types=1);

namespace App\Controller\Api\Shared;

use App\Controller\Api\ApiController;
use App\Controller\Api\ApiResponse;
use App\Controller\Api\Transformer\Shared\NicknameRandomShowTransformer;
use App\Exception\Shared\NicknameCouldNotBeGeneratedException;
use App\Message\Shared\NicknameRandomShowMessage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class NicknamesRandomShowController extends ApiController
{
    #[Route(
        path: '/nicknames/random',
        name: 'api_nickname_show',
        methods: ['GET'])
    ]
    public function __invoke(
        Request $request,
        NicknameRandomShowTransformer $nicknameRandomShowTransformer
    ): ApiResponse {
        try {
            $nickname = $this->dispatch(
                new NicknameRandomShowMessage($request->query->get('base_name')),
                getReturnedValue: true
            );

            return ApiResponse::ok(['data' => $nicknameRandomShowTransformer->transform($nickname)]);
        } catch (NicknameCouldNotBeGeneratedException $exception) {
            return ApiResponse::serviceUnavailable([
                ['propertyPath' => null, 'message' => $exception->getMessage()]
            ]);
        }
    }
}
