<?php

declare(strict_types=1);

namespace App\Controller\Api\Auth\Passwords;

use App\Controller\Api\ApiController;
use App\Controller\Api\ApiResponse;
use App\Controller\Api\Auth\Passwords\Request\PasswordsResetUpdateRequest;
use App\Controller\Api\RequestValidator;
use App\Exception\Auth\PasswordResetNotFound;
use App\Exception\Auth\ResetTokenExpiredException;
use App\Message\Auth\PasswordResetUpdateMessage;
use JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class PasswordsResetsUpdateController extends ApiController
{
    #[Route(
        path: '/passwords_resets/{token}',
        name: 'api_passwords_resets_update',
        methods: ['POST']
    )]
    public function __invoke(string $token, Request $request, RequestValidator $requestValidator): ApiResponse
    {
        try {
            $password = $this->decodeRequest($request)['password'] ?? null;

            $passwordsResetUpdateRequest = $requestValidator->validate(
                new PasswordsResetUpdateRequest($token, $password)
            );

            if ($passwordsResetUpdateRequest->hasViolations()) {
                return ApiResponse::unprocessableEntity($passwordsResetUpdateRequest->getViolations(asArray: true));
            }

            $this->dispatch(
                new PasswordResetUpdateMessage($token, $password)
            );

            return ApiResponse::empty();
        } catch (JsonException $exception) {
            return ApiResponse::badRequest([
                ['propertyPath' => null, 'message' => $exception->getMessage()]
            ]);
        } catch (ResetTokenExpiredException|PasswordResetNotFound $exception) {
            return ApiResponse::unprocessableEntity([
                ['propertyPath' => 'token', 'message' => $exception->getMessage()]
            ]);
        }
    }
}