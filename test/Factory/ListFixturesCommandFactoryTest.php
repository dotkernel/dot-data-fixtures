<?php

declare(strict_types=1);

namespace DotTest\DataFixtures\Factory;

use Dot\DataFixtures\Exception\NotFoundException;
use Dot\DataFixtures\Factory\ListFixturesCommandFactory;
use DotTest\DataFixtures\Fixtures\Container;
use DotTest\DataFixtures\Fixtures\Data\RoleFixture;
use DotTest\DataFixtures\Fixtures\EntityManagerFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

#[CoversClass(ListFixturesCommandFactory::class)]
class ListFixturesCommandFactoryTest extends TestCase
{
    public function testWillNotCreateServiceWithoutConfigService(): void
    {
        $this->expectException(NotFoundExceptionInterface::class);
        (new ListFixturesCommandFactory())(new Container());
    }

    #[DataProvider('invalidConfigProvider')]
    public function testWillNotCreateServiceWithoutFixturesPath(mixed $config): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Key `fixtures` not found in doctrine configuration.');
        (new ListFixturesCommandFactory())(new Container(['config' => $config]));
    }

    /**
     * @return iterable<string, array{mixed}>
     */
    public static function invalidConfigProvider(): iterable
    {
        yield 'config is null' => [null];
        yield 'config is a string' => ['config'];
        yield 'no doctrine key' => [[]];
        yield 'doctrine is a string' => [['doctrine' => 'doctrine']];
        yield 'no fixtures key' => [['doctrine' => []]];
        yield 'fixtures is not a string' => [['doctrine' => ['fixtures' => 123]]];
    }

    public function testCreatesCommandThatListsConfiguredFixtures(): void
    {
        $container = new Container([
            'config' => ['doctrine' => ['fixtures' => EntityManagerFactory::FIXTURES_PATH]],
        ]);

        $tester = new CommandTester((new ListFixturesCommandFactory())($container));

        $this->assertSame(Command::SUCCESS, $tester->execute([]));
        $this->assertStringContainsString(RoleFixture::class, $tester->getDisplay());
    }
}
