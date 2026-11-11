# Configuration

## Register ConfigProvider

After installation, register the package's ConfigProvider by adding the below line into your application config:

```php
\Dot\DataFixtures\ConfigProvider::class,
```

In `doctrine.global.php` (or your custom doctrine config file) add a new key `fixtures` in the `doctrine` array.
The value must be the path to the folder where your fixtures can be found.
The folder is scanned recursively, so fixtures can be grouped in subfolders.

Make sure the path is valid before proceeding to the next step.

### Example

The `// ...` comments stand for the rest of your existing configuration.

```php
return [
    'dependencies' => [
        // ...
    ],
    'doctrine'     => [
        // ...
        'fixtures' => getcwd() . '/data/doctrine/fixtures',
    ],
];
```

## Registering commands

The last step is to register the commands. We can register the commands to work with the default CLI that doctrine provides us. Create a new php file `bin/doctrine` (if you don't already have this file feel free to copy it from the below example)

```php
<?php

declare(strict_types=1);

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;

require_once 'vendor/autoload.php';

$container = require getcwd() . '/config/container.php';

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

The factories only use `Psr\Container\ContainerInterface`, so they work with any PSR-11 container that accepts a factory per service, such as `laminas/laminas-servicemanager`.
