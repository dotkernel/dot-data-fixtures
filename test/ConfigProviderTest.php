<?php

declare(strict_types=1);

namespace DotTest\DataFixtures;

use Dot\DataFixtures\Command\ExecuteFixturesCommand;
use Dot\DataFixtures\Command\ListFixturesCommand;
use Dot\DataFixtures\ConfigProvider;
use Dot\DataFixtures\Factory\ExecuteFixturesCommandFactory;
use Dot\DataFixtures\Factory\ListFixturesCommandFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ConfigProvider::class)]
class ConfigProviderTest extends TestCase
{
    public function testInvokeReturnsDependencies(): void
    {
        $provider = new ConfigProvider();

        $this->assertSame(['dependencies' => $provider->getDependencies()], $provider());
    }

    public function testRegistersFactoryForEachCommand(): void
    {
        $this->assertSame(
            [
                'factories' => [
                    ExecuteFixturesCommand::class => ExecuteFixturesCommandFactory::class,
                    ListFixturesCommand::class    => ListFixturesCommandFactory::class,
                ],
            ],
            (new ConfigProvider())->getDependencies()
        );
    }
}
