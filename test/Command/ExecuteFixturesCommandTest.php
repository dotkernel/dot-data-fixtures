<?php

declare(strict_types=1);

namespace DotTest\DataFixtures\Command;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Dot\DataFixtures\Command\ExecuteFixturesCommand;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionMethod;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;

class ExecuteFixturesCommandTest extends TestCase
{
    protected ExecuteFixturesCommand|MockObject $executeFixturesCommandMock;

    protected EntityManager|MockObject $entityManager;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->executeFixturesCommandMock = $this->createMock(ExecuteFixturesCommand::class);
        $this->entityManager              = $this->createMock(EntityManager::class);
    }

    public function testCreateCommand(): void
    {
        $this->assertInstanceOf(ExecuteFixturesCommand::class, $this->executeFixturesCommandMock);
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function testCommandWillExecute(): void
    {
        $configuration = $this->createMock(Configuration::class);
        $connection    = $this->createMock(Connection::class);
        $entityManager = $this->createMock(EntityManager::class);
        $entityManager->method('getConnection')->willReturn($connection);
        $connection->method('getConfiguration')->willReturn($configuration);
        $command    = new ExecuteFixturesCommand($entityManager, 'fixtures:execute');
        $reflection = new ReflectionMethod(ExecuteFixturesCommand::class, 'execute');

        $result = $reflection->invoke(
            $command,
            new ArgvInput(),
            new BufferedOutput()
        );
        $this->assertSame($result, Command::SUCCESS);
    }
}
