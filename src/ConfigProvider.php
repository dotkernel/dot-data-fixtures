<?php

declare(strict_types=1);

namespace Dot\DataFixtures;

use Dot\DataFixtures\Command\ExecuteFixturesCommand;
use Dot\DataFixtures\Command\ListFixturesCommand;
use Dot\DataFixtures\Factory\ExecuteFixturesCommandFactory;
use Dot\DataFixtures\Factory\ListFixturesCommandFactory;

use function getcwd;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'doctrine'     => [
                'fixtures' => getcwd() . '/data/doctrine/fixtures',
            ],
        ];
    }

    public function getDependencies(): array
    {
        return [
            'factories' => [
                ExecuteFixturesCommand::class => ExecuteFixturesCommandFactory::class,
                ListFixturesCommand::class    => ListFixturesCommandFactory::class,
            ],
        ];
    }
}
