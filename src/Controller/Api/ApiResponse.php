<?php

declare(strict_types=1);

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiResponse extends JsonResponse
{
    public static function ok($data = null): self
    {
        return self::fromPayload($data, self::HTTP_OK);
    }

    public static function created(string $location = null): self
    {
        return new self(
            null,
            self::HTTP_CREATED,
            ($location) ? ['location' => $location] : []
        );
    }

    public static function empty(): self
    {
        return new self(null, self::HTTP_NO_CONTENT);
    }

    public static function badRequest($data = null): self
    {
        return self::fromPayload(['error' => $data], self::HTTP_BAD_REQUEST);
    }

    public static function unprocessableEntity($data = null): self
    {
        return self::fromPayload(['error' => $data], self::HTTP_UNPROCESSABLE_ENTITY);
    }

    public static function unauthorized($data = null): self
    {
        return self::fromPayload(['error' => $data], self::HTTP_UNAUTHORIZED);
    }

    public static function forbidden($data = null): self
    {
        return self::fromPayload(['error' => $data], self::HTTP_FORBIDDEN);
    }

    public static function conflict($data = null): self
    {
        return self::fromPayload(['error' => $data], self::HTTP_CONFLICT);
    }

    public static function serviceUnavailable($data = null): self
    {
        return self::fromPayload(['error' => $data], self::HTTP_SERVICE_UNAVAILABLE);
    }

    public static function fromPayload(array $payload, int $status): self
    {
        return new self($payload, $status);
    }

    private function __construct($data = null, int $status = self::HTTP_OK, array $headers = [])
    {
        parent::__construct($data, $status, $headers);
    }
}
