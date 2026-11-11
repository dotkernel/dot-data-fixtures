# Usage

## Commands

### List fixtures command

This command will list all the available fixtures, by order of execution.

```shell
php ./bin/doctrine fixtures:list
```

For each fixture it shows the class name, the command that executes only that fixture, and when its file was last changed (in UTC).

### Execute fixtures command

This command will execute one or all fixtures.
Executing fixtures will **append** data to the tables: nothing is purged first.

To execute all the fixtures, run:

```shell
php ./bin/doctrine fixtures:execute
```

To execute a specific fixture, pass its path relative to the fixtures folder, without `.php`:

```shell
php ./bin/doctrine fixtures:execute --class=RoleLoader
```

A fixture in a subfolder, for example `data/doctrine/fixtures/User/UserLoader.php`, is executed with:

```shell
php ./bin/doctrine fixtures:execute --class=User/UserLoader
```

`fixtures:list` prints this exact command for every fixture.

`--class` must have a value. If the fixture does not exist, or the path points outside the fixtures folder, the command prints an error and exits with code `1`.

## Creating fixtures

When creating a new fixture, we have two requirements:

- Fixtures should be created in the folder we configured earlier, `data/doctrine/fixtures`, or in a subfolder of it.
- Fixtures should implement `FixtureInterface` and have a `load` method.

Create a new PHP file and copy the below code-block.

### Example

```php
<?php

declare(strict_types=1);

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

Fixtures can be ordered:

- by order
- by dependencies

Please refer to this link for further details on ordering fixtures:

https://www.doctrine-project.org/projects/doctrine-data-fixtures/en/latest/how-to/fixture-ordering.html#fixture-ordering
