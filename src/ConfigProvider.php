<?php

declare(strict_types=1);

namespace Dot\DataFixtures;

use Dot\DataFixtures\Command\ExecuteFixturesCommand;
use Dot\DataFixtures\Command\ListFixturesCommand;
use Dot\DataFixtures\Factory\ExecuteFixturesCommandFactory;
use Dot\DataFixtures\Factory\ListFixturesCommandFactory;

class ConfigProvider
{
    /**
     * @return array{dependencies: array{factories: array<class-string, class-string>}}
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    /**
     * @return array{factories: array<class-string, class-string>}
     */
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
