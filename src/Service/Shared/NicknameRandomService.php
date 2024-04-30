<?php

declare(strict_types=1);

namespace App\Service\Shared;

use App\Exception\Shared\NicknameCouldNotBeGeneratedException;
use App\Repository\UserRepository;

readonly class NicknameRandomService
{
    final public const MAX_RETRIES = 10;

    public function __construct(private UserRepository $userRepository)
    {
    }

    /**
     * @throws NicknameCouldNotBeGeneratedException
     */
    public function __invoke(?string $baseName = null): string
    {
        $retries = self::MAX_RETRIES;

        $baseName = strtolower(trim($baseName ?? ''));

        do {
            $nickname = $this->generateNickname($baseName);

            if (!$this->userRepository->findByNickname($nickname)) {
                return $nickname;
            }
        } while (--$retries);

        throw new NicknameCouldNotBeGeneratedException();
    }

    /**
     * @param string $baseName
     * @return string
     */
    public function generateNickname(string $baseName): string
    {
        $adjectives = ['happy', 'brave', 'curious', 'funny', 'smart'];
        $nouns = ['cat', 'dog', 'panda', 'bear', 'fox'];

        $adjective = $adjectives[array_rand($adjectives)];
        $noun = $baseName ?: $nouns[array_rand($nouns)];

        return $adjective . $noun . rand(10, 999);
    }
}