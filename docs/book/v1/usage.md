# Usage

## Commands

### List fixtures command

This command will list all the available fixtures, by order of execution.

```shell
php ./bin/doctrine fixtures:list
```

### Execute fixtures command

This command will execute all or one fixture.

To execute all the fixtures, run:

```shell
php ./bin/doctrine fixtures:execute
```

To execute a specific fixture, run:

```shell
php ./bin/doctrine fixtures:execute --class=RoleLoader
```

## Creating fixtures

When creating a new fixture we have 2 requirements:

- Fixtures should be created in the folder we configured earlier, `data/doctrine/fixtures`
- Fixtures should implement `FixtureInterface` and have a `load` method.
- Create a new php file and copy the below code-block.

### Example

```php
<?php

namespace Frontend\Fixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Frontend\User\Entity\UserRole;


class RoleLoader implements FixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $adminRole = new UserRole();
        $adminRole->setName('admin');

        $userRole = new UserRole();
        $userRole->setName('user');
        
        $guestRole = new UserRole();
        $guestRole->setName('guest');
        
        $manager->persist($adminRole);
        $manager->persist($userRole);
        $manager->persist($guestRole);

        $manager->flush();
    }
}
```

## Ordering fixtures

Fixtures can we ordered using 2 methods:

- by order
- by dependencies

Please refer to this link for further details on ordering fixtures :

https://www.doctrine-project.org/projects/doctrine-data-fixtures/en/latest/how-to/fixture-ordering.html#fixture-ordering
