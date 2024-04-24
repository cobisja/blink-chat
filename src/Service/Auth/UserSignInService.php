<?php

declare(strict_types=1);

namespace App\Service\Auth;

use App\Exception\Auth\BadCredentialsException;
use App\Repository\UserRepository;
use JetBrains\PhpStorm\ArrayShape;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

readonly class UserSignInService
{
    public function __construct(
        private UserRepository $userRepository,
        private JWTTokenManagerInterface $JWTManager
    ) {
    }

    /**
     * @throws BadCredentialsException
     */
    #[ArrayShape([
        'token' => "string",
        'user' => "mixed"
    ])]
    public function __invoke(string $email, string $password): array
    {
        if (!$user = $this->userRepository->findOneBy(['email' => $email])) {
            throw new BadCredentialsException();
        }

        if (!password_verify($password, $user->getPassword())) {
            throw new BadCredentialsException();
        }

        $payLoad = [
            'nickname' => $user->getNickname(),
        ];

        $token = $this->JWTManager->createFromPayload($user, $payLoad);

        return [
            'token' => $token,
            'user' => $user,
        ];
    }
}
