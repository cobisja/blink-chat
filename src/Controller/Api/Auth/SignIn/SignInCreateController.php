<?php

declare(strict_types=1);

namespace App\Controller\Api\Auth\SignIn;

use App\Controller\Api\ApiController;
use App\Controller\Api\ApiResponse;
use App\Controller\Api\Auth\SignIn\Request\SignInCreateRequest;
use App\Controller\Api\RequestValidator;
use App\Controller\Api\Transformer\Auth\SignInTransformer;
use App\Exception\Auth\BadCredentialsException;
use App\Message\Auth\UserSignInMessage;
use JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class SignInCreateController extends ApiController
{
    #[Route(
        path: '/sign-in',
        name: 'api_sign_in_create',
        methods: ['POST']
    )]
    public function __invoke(
        Request $request,
        RequestValidator $requestValidator,
        SignInTransformer $signInTransformer
    ): JsonResponse {
        try {
            $requestData = $this->decodeRequest($request);

            $signInRequest = $requestValidator->validate(
                new SignInCreateRequest($requestData['email'] ?? null, $requestData['password'] ?? null)
            );

            if ($signInRequest->hasViolations()) {
                return ApiResponse::unprocessableEntity($signInRequest->getViolations(asArray: true));
            }

            $authInfo = $this->query(
                new UserSignInMessage($requestData['email'], $requestData['password'])
            );

            return ApiResponse::ok(['data' => $signInTransformer->transform($authInfo)]);
        } catch (JsonException $exception) {
            return ApiResponse::badRequest(['message' => $exception->getMessage()]);
        } catch (BadCredentialsException $exception) {
            return ApiResponse::unauthorized(['message' => $exception->getMessage()]);
        }
    }
}
