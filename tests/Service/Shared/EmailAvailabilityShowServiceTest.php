<?php

namespace App\Tests\Service\Shared;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Shared\EmailAvailabilityShowService;
use PHPUnit\Framework\TestCase;

class EmailAvailabilityShowServiceTest extends TestCase
{
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
    }

    /**
     * @test
     * @dataProvider availabilityFixtures
     */
    public function it_should_returns_the_email_availability(?User $user, bool $expectedAvailability): void
    {
        $email = 'test@test.test';

        $this
            ->userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->willReturn($user);

        $actualAvailability = (new EmailAvailabilityShowService($this->userRepository))($email);

        $this->assertSame($expectedAvailability, $actualAvailability);
    }

    private function availabilityFixtures(): array
    {
        return [
            [null, true],
            [new User(), false],
        ];
    }
}
