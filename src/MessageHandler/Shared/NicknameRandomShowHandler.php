<?php

declare(strict_types=1);

namespace App\MessageHandler\Shared;

use App\Exception\Shared\NicknameCouldNotBeGeneratedException;
use App\Message\Shared\NicknameRandomShowMessage;
use App\Service\Shared\NicknameRandomService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class NicknameRandomShowHandler
{
    public function __construct(private NicknameRandomService $nicknameGeneratorCreateService)
    {
    }

    /**
     * @throws NicknameCouldNotBeGeneratedException
     */
    public function __invoke(NicknameRandomShowMessage $message): string
    {
        return ($this->nicknameGeneratorCreateService)($message->baseName);
    }
}
