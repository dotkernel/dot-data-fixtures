# Installation

## Requirements

- **PHP**: 8.3, 8.4, 8.5 or 8.6
- **doctrine/orm**: 3.x
- **doctrine/data-fixtures**: 2.x
- **symfony/console**: 7.x or 8.x (8.x requires PHP 8.4 or later)
- a PSR-11 container that provides the `config` service and the `Doctrine\ORM\EntityManager` service

## Install the package

Install dotkernel/dot-data-fixtures by executing the following Composer command in your project directory:

```shell
composer require dotkernel/dot-data-fixtures
```

Upgrading from 1.x? Read [Upgrading](upgrading.md) first.
