<?php

declare(strict_types=1);

namespace DotTest\DataFixtures\Factory;

use Doctrine\ORM\EntityManager;
use Dot\DataFixtures\Exception\NotFoundException;
use Dot\DataFixtures\Factory\ExecuteFixturesCommandFactory;
use DotTest\DataFixtures\Fixtures\Container;
use DotTest\DataFixtures\Fixtures\EntityManagerFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Container\NotFoundExceptionInterface;
use stdClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

#[CoversClass(ExecuteFixturesCommandFactory::class)]
class ExecuteFixturesCommandFactoryTest extends TestCase
{
    public function testWillNotCreateServiceWithoutEntityManager(): void
    {
        $container = new Container([
            'config' => ['doctrine' => ['fixtures' => EntityManagerFactory::FIXTURES_PATH]],
        ]);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('EntityManager not found.');
        (new ExecuteFixturesCommandFactory())($container);
    }

    public function testWillNotCreateServiceWithoutConfigService(): void
    {
        $container = new Container([EntityManager::class => EntityManagerFactory::create()]);

        $this->expectException(NotFoundExceptionInterface::class);
        (new ExecuteFixturesCommandFactory())($container);
    }

    #[DataProvider('invalidConfigProvider')]
    public function testWillNotCreateServiceWithoutFixturesPath(mixed $config): void
    {
        $container = new Container([
            EntityManager::class => EntityManagerFactory::create(),
            'config'             => $config,
        ]);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Key `fixtures` not found in doctrine configuration.');
        (new ExecuteFixturesCommandFactory())($container);
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

    public function testWillNotCreateServiceWithWrongEntityManagerType(): void
    {
        $container = new Container([
            EntityManager::class => new stdClass(),
            'config'             => ['doctrine' => ['fixtures' => EntityManagerFactory::FIXTURES_PATH]],
        ]);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('is not an instance of `Doctrine\ORM\EntityManagerInterface`');
        (new ExecuteFixturesCommandFactory())($container);
    }

    public function testCreatesCommandThatExecutesConfiguredFixtures(): void
    {
        $entityManager = EntityManagerFactory::create();
        $container     = new Container([
            EntityManager::class => $entityManager,
            'config'             => ['doctrine' => ['fixtures' => EntityManagerFactory::FIXTURES_PATH]],
        ]);

        $tester = new CommandTester((new ExecuteFixturesCommandFactory())($container));

        $this->assertSame(Command::SUCCESS, $tester->execute(['--class' => 'RoleFixture']));
        $this->assertSame(['admin'], EntityManagerFactory::roleNames($entityManager));
    }
}
