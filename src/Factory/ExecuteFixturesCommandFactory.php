<?php

declare(strict_types=1);

namespace Dot\DataFixtures\Factory;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Dot\DataFixtures\Command\ExecuteFixturesCommand;
use Dot\DataFixtures\Exception\NotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function is_array;
use function is_string;
use function sprintf;

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

        $config = $container->get('config');
        $path   = is_array($config) && is_array($config['doctrine'] ?? null)
            ? $config['doctrine']['fixtures'] ?? null
            : null;
        if (! is_string($path)) {
            throw new NotFoundException('Key `fixtures` not found in doctrine configuration.');
        }

        $entityManager = $container->get(EntityManager::class);
        if (! $entityManager instanceof EntityManagerInterface) {
            throw new NotFoundException(
                sprintf('Service `%s` is not an instance of `%s`.', EntityManager::class, EntityManagerInterface::class)
            );
        }

        return new ExecuteFixturesCommand(new Loader(), new ORMExecutor($entityManager), $path);
    }
}
