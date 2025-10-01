<?php

namespace App\Infrastructure\Persistence\DataFixtures;

use App\Domain\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        UserFactory::createMany(10);
    }
}
