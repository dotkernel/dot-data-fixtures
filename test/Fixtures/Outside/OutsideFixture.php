<?php

declare(strict_types=1);

namespace DotTest\DataFixtures\Fixtures\Outside;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use DotTest\DataFixtures\Fixtures\Entity\Role;

/**
 * Lives outside the fixtures directory: executing it through --class must be refused.
 */
class OutsideFixture implements FixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $manager->persist(new Role('outside'));
        $manager->flush();
    }
}
