<?php

declare(strict_types=1);

namespace DotTest\DataFixtures\Fixtures\Data;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use DotTest\DataFixtures\Fixtures\Entity\Role;

class RoleFixture implements FixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $manager->persist(new Role('admin'));
        $manager->flush();
    }
}
