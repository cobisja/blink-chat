<?php

declare(strict_types=1);

namespace App\Controller\Api;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestValidator
{
    private ConstraintViolationListInterface $violations;

    public function __construct(private readonly ValidatorInterface $validator)
    {
    }

    public function validate($request): static
    {
        $this->violations = $this->validator->validate($request);

        return $this;
    }

    public function getViolations(bool $asArray = false): ConstraintViolationListInterface|array
    {
        if (!$asArray) {
            return $this->violations;
        }

        return array_map(
            static fn($error) => ["propertyPath" => $error->getPropertyPath(), "message" => $error->getMessage()],
            iterator_to_array($this->violations)
        );
    }

    public function hasViolations(): bool
    {
        return 0 < count($this->violations ?? []);
    }
}
