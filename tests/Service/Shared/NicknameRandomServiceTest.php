<?php

namespace App\Tests\Service\Shared;

use App\Entity\User;
use App\Exception\Shared\NicknameCouldNotBeGeneratedException;
use App\Exception\User\UserNotFoundException;
use App\Repository\UserRepository;
use App\Service\Shared\NicknameRandomService;
use PHPUnit\Framework\TestCase;

class NicknameRandomServiceTest extends TestCase
{
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
    }

    /**
     * @test
     * @dataProvider serviceFixtures
     */
    public function it_should_triggers_could_not_be_generated_exception_when_exhausted_retries(?string $baseName): void
    {
        $this->expectException(NicknameCouldNotBeGeneratedException::class);

        $this
            ->userRepository
            ->expects($this->any())
            ->method('findByNickname')
            ->willReturn(new User());

        (new NicknameRandomService($this->userRepository))($baseName);
    }

    /**
     * @test
     * @dataProvider serviceFixtures
     * @throws NicknameCouldNotBeGeneratedException
     */
    public function it_should_generate_a_nickname($baseName): void
    {
        $this
            ->userRepository
            ->expects($this->any())
            ->method('findByNickname')
            ->willReturn(null);

        $nickname = (new NicknameRandomService($this->userRepository))($baseName);

        $this->assertNotEmpty($nickname);

        $baseName && $this->assertStringContainsString($baseName, $nickname);
    }

    private function serviceFixtures(): array
    {
        return [
            [null],
            ['test']
        ];
    }
}
