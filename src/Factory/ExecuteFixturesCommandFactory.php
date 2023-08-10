<?php

declare(strict_types=1);

namespace Dot\DataFixtures\Factory;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Dot\DataFixtures\Command\ExecuteFixturesCommand;
use Dot\DataFixtures\Exception\NotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function is_string;

class ExecuteFixturesCommandFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundException
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): ExecuteFixturesCommand
    {
        if (! $container->has(EntityManager::class)) {
            throw new NotFoundException('EntityManager not found.');
        }

        if (! $container->has(ORMPurger::class)) {
            throw new NotFoundException('ORMPurger not found. ');
        }

        if (! $container->has(Loader::class)) {
            throw new NotFoundException('Loader not found. ');
        }

        if (! $container->has(ORMExecutor::class)) {
            throw new NotFoundException('ORMExecutor not found. ');
        }

        $path = $container->get('config')['doctrine']['fixtures'] ?? null;
        if (! is_string($path)) {
            throw new NotFoundException('Key `fixtures` not found in doctrine configuration.');
        }

        return new ExecuteFixturesCommand(
            $container->get(EntityManager::class),
            $container->get(Loader::class),
            $container->get(ORMPurger::class),
            $container->get(ORMExecutor::class),
            $path
        );
    }
}
