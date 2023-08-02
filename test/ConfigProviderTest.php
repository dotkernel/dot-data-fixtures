<?php

declare(strict_types=1);

namespace DotTest\DataFixtures;

use Dot\DataFixtures\Command\ExecuteFixturesCommand;
use Dot\DataFixtures\Command\ListFixturesCommand;
use Dot\DataFixtures\ConfigProvider;
use Dot\DataFixtures\Factory\ExecuteFixturesCommandFactory;
use Dot\DataFixtures\Factory\ListFixturesCommandFactory;
use PHPUnit\Framework\TestCase;

class ConfigProviderTest extends TestCase
{
    protected array $config;

    protected function setup(): void
    {
        $this->config = (new ConfigProvider())();
    }

    public function testHasDependencies(): void
    {
        $this->assertArrayHasKey('dependencies', $this->config);
    }

    public function testDependenciesHasFactories(): void
    {
        $this->assertArrayHasKey('factories', $this->config['dependencies']);

        $factories = $this->config['dependencies']['factories'];
        $this->assertArrayHasKey(ExecuteFixturesCommand::class, $factories);
        $this->assertSame(ExecuteFixturesCommandFactory::class, $factories[ExecuteFixturesCommand::class]);
        $this->assertArrayHasKey(ListFixturesCommand::class, $factories);
        $this->assertSame(ListFixturesCommandFactory::class, $factories[ListFixturesCommand::class]);
    }
}
