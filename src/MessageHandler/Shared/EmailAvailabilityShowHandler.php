<?php

declare(strict_types=1);

namespace App\MessageHandler\Shared;

use App\Message\Shared\EmailAvailabilityShowMessage;
use App\Service\Shared\EmailAvailabilityShowService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class EmailAvailabilityShowHandler
{
    public function __construct(private EmailAvailabilityShowService $emailAvailabilityShowService)
    {
    }

    public function __invoke(EmailAvailabilityShowMessage $message): bool
    {
        return ($this->emailAvailabilityShowService)($message->email);
    }
}
