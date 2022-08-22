<?php


namespace Dot\DataFixtures\Factory;


use Doctrine\ORM\EntityManager;
use Dot\DataFixtures\Command\ExecuteFixturesCommand;
use Dot\DataFixtures\Exception\NotFoundException;
use Psr\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class ExecuteFixturesCommandFactory
 * @package Dot\DataFixtures\Factory
 */
class ExecuteFixturesCommandFactory
{
    /**
     * @param ContainerInterface $container
     * @return ExecuteFixturesCommand
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): ExecuteFixturesCommand
    {
        $entityManager = $container->has(EntityManager::class) ?
            $container->get(EntityManager::class) :
            throw new NotFoundException('EntityManager not found.');

        $path = $container->get('config')['doctrine']['fixtures'] ?? '';
        if (! is_string($path)) {
            throw new NotFoundException('Key `fixtures` not found in doctrine configuration.');
        }

        return new ExecuteFixturesCommand($entityManager, $path);
    }
}
