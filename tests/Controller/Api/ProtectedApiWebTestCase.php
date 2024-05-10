<?php

declare(strict_types=1);

namespace App\Tests\Controller\Api;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;

class ProtectedApiWebTestCase extends ApiWebTestCase
{
    private JWTManager $jwtTokenManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->jwtTokenManager = self::getContainer()->get('lexik_jwt_authentication.jwt_manager');
    }

    protected function authenticateUser(User $user, string|array $withRoles = 'ROLE_USER'): string
    {
        $user->setRoles(is_array($withRoles) ? $withRoles : [$withRoles]);

        return $this->jwtTokenManager->createFromPayload($user, []);
    }
}
