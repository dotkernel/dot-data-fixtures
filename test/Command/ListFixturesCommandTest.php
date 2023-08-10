<?php

declare(strict_types=1);

namespace DotTest\DataFixtures\Command;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Dot\DataFixtures\Command\ListFixturesCommand;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionMethod;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;

use function getcwd;

class ListFixturesCommandTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testWillCreateCommand(): void
    {
        $loader   = $this->createMock(Loader::class);
        $purger   = $this->createMock(ORMPurger::class);
        $executor = $this->createMock(ORMExecutor::class);
        $path     = getcwd() . '/data/doctrine/fixtures';
        $command  = new ListFixturesCommand($loader, $purger, $executor, $path);
        $this->assertInstanceOf(ListFixturesCommand::class, $command);
    }

    /**
     * @throws Exception
     * @throws ReflectionException
     */
    public function testWillExecuteCommand(): void
    {
        $configuration = $this->createMock(Configuration::class);
        $connection    = $this->createMock(Connection::class);
        $entityManager = $this->createMock(EntityManager::class);
        $eventManager  = $this->createMock(EventManager::class);
        $loader        = $this->createMock(Loader::class);
        $purger        = $this->createMock(ORMPurger::class);
        $executor      = $this->createMock(ORMExecutor::class);
        $connection->method('getConfiguration')->willReturn($configuration);
        $entityManager->method('getConnection')->willReturn($connection);
        $entityManager->method('getEventManager')->willReturn($eventManager);
        $purger->method('getObjectManager')->willReturn($entityManager);
        $loader->method('getFixtures')->willReturnMap([
            [0 => []],
        ]);
        $path = getcwd() . '/data/doctrine/fixtures';

        $command    = new ListFixturesCommand($loader, $purger, $executor, $path);
        $reflection = new ReflectionMethod(ListFixturesCommand::class, 'execute');

        $result = $reflection->invoke(
            $command,
            new ArgvInput(),
            new BufferedOutput()
        );
        $this->assertSame($result, Command::SUCCESS);
    }

    /**
     * @throws Exception
     */
    public function testFunctions(): void
    {
        $loader      = $this->createMock(Loader::class);
        $purger      = $this->createMock(ORMPurger::class);
        $executor    = $this->createMock(ORMExecutor::class);
        $path        = getcwd() . '/data/doctrine/fixtures';
        $command     = new ListFixturesCommand($loader, $purger, $executor, $path);
        $defaultName = $command->getName();
        $description = $command->getDescription();
        $this->assertSame('fixtures:list', $defaultName);
        $this->assertSame('List all available fixtures.', $description);
    }
}
