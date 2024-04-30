<?php

declare(strict_types=1);

namespace App\Controller\Api\Shared;

use App\Controller\Api\ApiController;
use App\Controller\Api\ApiResponse;
use App\Controller\Api\RequestValidator;
use App\Controller\Api\Shared\Request\EmailAvailabilityShowRequest;
use App\Controller\Api\Transformer\Shared\EmailAvailabilityShowTransformer;
use App\Message\Shared\EmailAvailabilityShowMessage;
use JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class EmailAvailabilityShowController extends ApiController
{
    #[Route('/emails/availability', name: 'api_emails_availability_show', methods: ['GET'])]
    public function __invoke(
        Request $request,
        RequestValidator $requestValidator,
        EmailAvailabilityShowTransformer $availabilityShowTransformer
    ): ApiResponse {
        try {
            $email = $request->query->get('email');

            $emailAvailabilityShowRequest = $requestValidator->validate(
                new EmailAvailabilityShowRequest($email)
            );

            if ($emailAvailabilityShowRequest->hasViolations()) {
                return ApiResponse::unprocessableEntity($emailAvailabilityShowRequest->getViolations(asArray: true));
            }

            $emailAvailability = $this->query(
                new EmailAvailabilityShowMessage($email)
            );

            return ApiResponse::ok(['data' => $availabilityShowTransformer->transform($emailAvailability)]);
        } catch (JsonException $exception) {
            return ApiResponse::badRequest([
                ['propertyPath' => null, 'message' => $exception->getMessage()]
            ]);
        }
    }
}