#dot-data-fixtures

This package provides a CLI interface for interacting with doctrine/data-fixtures.

### Requirements
- PHP >= 7.4
- doctrine/data-fixtures => 1.5

## Installation

Run the following command in you project directory
```bash
$ composer require dotkernel/dot-data-fixtures
```

Next, register the package's ConfigProvider into your application config.

```\Dot\DataFixtures\ConfigProvider::class,```

In ``doctrine.global.php`` (or your custom doctrine config file) add a new key `fixtures`, in the `doctrine`
array, the value should be a valid path to a folder where your fixtures can be found.

**Make sure the path is valid before proceeding to the next step.**

#### Example :
```
return [
    'dependencies' => [ ... ],
    'doctrine' => [
        ...,
        'fixtures' => getcwd() . '/data/doctrine/fixtures',
    ],
];
```

The last step is to register the commands. 
We can register the commands to work with the default CLI that doctrine provides us.
Go to `bin/doctrine` (if you don't already have this file feel free to copy it from the below example)

```php
#!/usr/bin/env php
<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;

require_once 'vendor/autoload.php';

$container = require getcwd() . '/config/container.php' ;

$entityManager = $container->get(\Doctrine\ORM\EntityManager::class);

$commands = [
    $container->get(Dot\DataFixtures\Command\ExecuteFixturesCommand::class),
    $container->get(Dot\DataFixtures\Command\ListFixturesCommand::class),
];

ConsoleRunner::run(
    new SingleManagerProvider($entityManager),
    $commands
);
```

## Creating fixtures

When creating a new fixture we have 2 requirements :
- Fixtures should be created in the path we configured earlier.
- Fixtures should implement ``FixtureInterface`` and have a ``load`` method.

#### Example : 

```php
class RoleLoader implements FixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $admin = new UserRole();
        $admin->setName('admin');

        $guest = new UserRole();
        $guest->setName('guest');


        $manager->persist($admin);
        $manager->persist($guest);

        $manager->flush();
    }
}
```

## Usage

**List fixtures command** - will list all available fixtures printing the namespace, generating the command to run a specific fixture and the last updated at date.
````bash
php bin/doctrine fixtures:list
````

**Execute fixtures command** - this command will execute all or one fixture.
- To execute all the fixtures run : 
```bash
php bin/doctrine fixtures:execute
```
- To execute a specific fixture run :
```bash
php bin/doctrine fixtures:execute --class=RoleLoader
```
