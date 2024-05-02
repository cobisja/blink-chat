<?php

declare(strict_types=1);

namespace App\Controller\Api\Auth\Passwords;

use App\Controller\Api\ApiController;
use App\Controller\Api\ApiResponse;
use App\Controller\Api\Auth\Passwords\Request\PasswordsResetCreateRequest;
use App\Controller\Api\RequestValidator;
use App\Message\Auth\PasswordResetCreateMessage;
use JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class PasswordsResetsCreateController extends ApiController
{
    #[Route(
        path: '/passwords_resets',
        name: 'api_passwords_resets_create',
        methods: ['POST']
    )]
    public function __invoke(Request $request, RequestValidator $requestValidator): ApiResponse
    {
        try {
            $email = $this->decodeRequest($request)['email'] ?? null;

            $passwordsResetCreateRequest = $requestValidator->validate(
                new PasswordsResetCreateRequest($email)
            );

            if ($passwordsResetCreateRequest->hasViolations()) {
                return ApiResponse::unprocessableEntity($passwordsResetCreateRequest->getViolations(asArray: true));
            }

            $this->dispatch(
                new PasswordResetCreateMessage($email)
            );

            return ApiResponse::created();
        } catch (JsonException $exception) {
            return ApiResponse::badRequest([
                ['propertyPath' => null, 'message' => $exception->getMessage()]
            ]);
        }
    }
}