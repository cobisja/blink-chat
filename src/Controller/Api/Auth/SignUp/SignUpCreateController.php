<?php

declare(strict_types=1);

namespace App\Controller\Api\Auth\SignUp;

use App\Controller\Api\ApiController;
use App\Controller\Api\ApiResponse;
use App\Controller\Api\Auth\SignUp\Request\SignUpCreateRequest;
use App\Controller\Api\RequestValidator;
use App\Exception\Auth\EmailAlreadyTakenException;
use App\Exception\Auth\NicknameAlreadyTakenException;
use App\Exception\Auth\PasswordConfirmationDoesNotMatchException;
use App\Message\Auth\UserSignUpMessage;
use JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class SignUpCreateController extends ApiController
{
    #[Route(
        path: '/sign-up',
        name: 'api_sign_up_create',
        methods: ['POST']
    )]
    public function __invoke(Request $request, RequestValidator $requestValidator): ApiResponse
    {
        try {
            $requestData = $this->decodeRequest($request);

            $signUpRequest = $requestValidator->validate(
                new SignUpCreateRequest(
                    email: $requestData['email'] ?? null,
                    password: $requestData['password'] ?? null,
                    passwordConfirmation: $requestData['password_confirmation'] ?? null,
                    name: $requestData['name'] ?? null,
                    lastname: $requestData['lastname'] ?? null,
                    nickname: $requestData['nickname'] ?? null,
                )
            );

            if ($signUpRequest->hasViolations()) {
                return ApiResponse::unprocessableEntity($signUpRequest->getViolations(asArray: true));
            }

            $this->dispatch(
                new UserSignUpMessage(
                    email: $requestData['email'],
                    password: $requestData['password'],
                    passwordConfirmation: $requestData['password_confirmation'],
                    name: $requestData['name'],
                    lastname: $requestData['lastname'],
                    nickname: $requestData['nickname'],
                )
            );

            return ApiResponse::created();
        } catch (JsonException $exception) {
            return ApiResponse::badRequest([
                ['propertyPath' => null, 'message' => $exception->getMessage()]
            ]);
        } catch (EmailAlreadyTakenException $exception) {
            return ApiResponse::conflict(['propertyPath' => 'email', 'message' => $exception->getMessage()]);
        } catch (NicknameAlreadyTakenException $exception) {
            return ApiResponse::conflict(['propertyPath' => 'nickname', 'message' => $exception->getMessage()]);
        }
    }
}
