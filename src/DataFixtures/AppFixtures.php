<?php

namespace App\DataFixtures;

use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::createOne([
            'email' => 'john.doe@example.org',
            'name' => 'john',
            'lastname' => 'doe',
        ]);
        UserFactory::createMany(100);
    }
}
