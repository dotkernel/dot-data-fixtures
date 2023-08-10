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
use Dot\DataFixtures\Command\ExecuteFixturesCommand;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionMethod;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;

use function getcwd;

class ExecuteFixturesCommandTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testWillCreateCommand(): void
    {
        $entityManager = $this->createMock(EntityManager::class);
        $loader        = $this->createMock(Loader::class);
        $purger        = $this->createMock(ORMPurger::class);
        $executor      = $this->createMock(ORMExecutor::class);
        $path          = getcwd() . '/data/doctrine/fixtures';
        $command       = new ExecuteFixturesCommand($entityManager, $loader, $purger, $executor, $path);
        $this->assertInstanceOf(ExecuteFixturesCommand::class, $command);
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
            [
                [],
            ],
        ]);
        $path = getcwd() . '/data/doctrine/fixtures';

        $command    = new ExecuteFixturesCommand($entityManager, $loader, $purger, $executor, $path);
        $reflection = new ReflectionMethod(ExecuteFixturesCommand::class, 'execute');
        $result     = $reflection->invoke($command, new ArgvInput([], $command->getDefinition()), new BufferedOutput());
        $this->assertSame($result, Command::SUCCESS);
    }

    /**
     * @throws Exception
     */
    public function testConfigure(): void
    {
        $entityManager = $this->createMock(EntityManager::class);
        $loader        = $this->createMock(Loader::class);
        $purger        = $this->createMock(ORMPurger::class);
        $executor      = $this->createMock(ORMExecutor::class);

        $path        = getcwd() . '/data/doctrine/fixtures';
        $command     = new ExecuteFixturesCommand($entityManager, $loader, $purger, $executor, $path);
        $defaultName = $command->getName();
        $description = $command->getDescription();
        $options     = $command->getDefinition()->getOption('class');

        $this->assertSame('fixtures:execute', $defaultName);
        $this->assertSame('Executes one or multiple fixtures.', $description);
        $this->assertSame('class', $options->getName());
        $this->assertEmpty($options->getShortcut());
        $this->assertFalse($options->getDefault());
        $this->assertSame('Execute a specific fixture.', $options->getDescription());
    }
}
