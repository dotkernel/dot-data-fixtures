<?php

declare(strict_types=1);

namespace Dot\DataFixtures\Factory;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Dot\DataFixtures\Command\ListFixturesCommand;
use Dot\DataFixtures\Exception\NotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function is_string;

class ListFixturesCommandFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundException
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): ListFixturesCommand
    {
        $path = $container->get('config')['doctrine']['fixtures'] ?? null;
        if (! is_string($path)) {
            throw new NotFoundException('Key `fixtures` not found in doctrine configuration.');
        }

        return new ListFixturesCommand(
            $container->get(Loader::class),
            $container->get(ORMPurger::class),
            $container->get(ORMExecutor::class),
            $path
        );
    }
}
