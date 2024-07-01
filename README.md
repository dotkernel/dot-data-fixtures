# dot-data-fixtures

> [!IMPORTANT]
> dot-data-fixtures is a wrapper on top of [data-fixtures](https://github.com/doctrine/data-fixtures)
>
> ![OSS Lifecycle](https://img.shields.io/osslifecycle/doctrine/data-fixtures)

![OSS Lifecycle](https://img.shields.io/osslifecycle/dotkernel/dot-data-fixtures)
![PHP from Packagist (specify version)](https://img.shields.io/packagist/php-v/dotkernel/dot-data-fixtures/1.1.3)

[![GitHub issues](https://img.shields.io/github/issues/dotkernel/dot-data-fixtures)](https://github.com/dotkernel/dot-data-fixtures/issues)
[![GitHub forks](https://img.shields.io/github/forks/dotkernel/dot-data-fixtures)](https://github.com/dotkernel/dot-data-fixtures/network)
[![GitHub stars](https://img.shields.io/github/stars/dotkernel/dot-data-fixtures)](https://github.com/dotkernel/dot-data-fixtures/stargazers)
[![GitHub license](https://img.shields.io/github/license/dotkernel/dot-data-fixtures)](https://github.com/dotkernel/dot-data-fixtures/blob/1.0/LICENSE)

[![Build Static](https://github.com/dotkernel/dot-data-fixtures/actions/workflows/continuous-integration.yml/badge.svg?branch=1.0)](https://github.com/dotkernel/dot-data-fixtures/actions/workflows/continuous-integration.yml)
[![codecov](https://codecov.io/gh/dotkernel/dot-data-fixtures/graph/badge.svg?token=PGOXZOZAB0)](https://codecov.io/gh/dotkernel/dot-data-fixtures)

[![SymfonyInsight](https://insight.symfony.com/projects/6bac345c-9548-47ec-ab4a-25773a98ed03/big.svg)](https://insight.symfony.com/projects/6bac345c-9548-47ec-ab4a-25773a98ed03)

This package provides a CLI interface for interacting with doctrine/data-fixtures.

**Executing fixtures will **append** data to the tables.**

## Requirements

- PHP >= 8.1
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

### Example

```php
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
Create a new php file `bin/doctrine` (if you don't already have this file feel free to copy it from the below example)

```php
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

## Usage

**List fixtures command** - will list all the available fixtures, by order of execution.

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

## Creating fixtures

When creating a new fixture we have 2 requirements :

- Fixtures should be created in the folder we configured earlier. ``data/doctrine/fixtures``
- Fixtures should implement ``FixtureInterface`` and have a ``load`` method.
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

Fixtures can we ordered using 2 methods :

- by order
- by dependencies

Please refer to this link for further details on ordering fixtures:

https://www.doctrine-project.org/projects/doctrine-data-fixtures/en/latest/how-to/fixture-ordering.html#fixture-ordering
