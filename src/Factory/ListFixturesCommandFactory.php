<?php


namespace Dot\DataFixtures\Factory;

use Dot\DataFixtures\Command\ListFixturesCommand;
use Psr\Container\ContainerInterface;

class ListFixturesCommandFactory
{
    /**
     * @param ContainerInterface $container
     * @return ListFixturesCommand
     */
    public function __invoke(ContainerInterface $container): ListFixturesCommand
    {
        $path = $container->get('config')['doctrine']['fixtures'] ?? '';
        if (! is_string($path)) {
            throw new NotFoundException('Key `fixtures` not found in doctrine configuration.');
        }

        return new ListFixturesCommand($path);
    }
}
