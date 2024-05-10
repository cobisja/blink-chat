<?php

declare(strict_types=1);

namespace App\Tests\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiWebTestCase extends WebTestCase
{
    protected KernelBrowser $client;
    protected ?EntityManagerInterface $entityManager;
    protected UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = self::getContainer()->get('doctrine')?->getManager();
        $this->userRepository = $this->entityManager->getRepository(User::class);
    }

    protected function createTestUser(array $userData = null): User
    {
        if (!$userData) {
            $userData = [
                'email' => 'test@test.test',
                'password' => 'test-test',
                'name' => 'test',
                'lastname' => 'test',
                'nickname' => 'test-test'
            ];
        }

        $user = new User();

        $user->setEmail($userData['email']);
        $user->setPassword(password_hash($userData['password'], PASSWORD_DEFAULT));
        $user->setName($userData['name']);
        $user->setLastName($userData['lastname']);
        $user->setNickname($userData['nickname']);
        $user->setQueryField();

        $this->userRepository->save($user);

        return $user;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}
