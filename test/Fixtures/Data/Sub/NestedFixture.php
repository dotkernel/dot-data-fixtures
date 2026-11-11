<?php

declare(strict_types=1);

namespace DotTest\DataFixtures\Fixtures\Data\Sub;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use DotTest\DataFixtures\Fixtures\Entity\Role;

class NestedFixture implements FixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $manager->persist(new Role('nested'));
        $manager->flush();
    }
}
