<?php

declare(strict_types=1);

namespace DotTest\DataFixtures\Command;

use Dot\DataFixtures\Command\ListFixturesCommand;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionMethod;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;

class ListFixturesCommandTest extends TestCase
{
    protected ListFixturesCommand|MockObject $listFixturesCommandMock;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->listFixturesCommandMock = $this->createMock(ListFixturesCommand::class);
    }

    public function testCreate(): void
    {
        $this->assertInstanceOf(ListFixturesCommand::class, $this->listFixturesCommandMock);
    }

    /**
     * @throws ReflectionException
     */
    public function testCommandWillExecute(): void
    {
        $command    = new ListFixturesCommand('test');
        $reflection = new ReflectionMethod(ListFixturesCommand::class, 'execute');

        $result = $reflection->invoke(
            $command,
            new ArgvInput(),
            new BufferedOutput()
        );
        $this->assertSame($result, Command::SUCCESS);
    }

    public function testFunctions(): void
    {
        $command     = new ListFixturesCommand('test');
        $defaultName = $command->getName();
        $description = $command->getDescription();
        $this->assertSame('fixtures:list', $defaultName);
        $this->assertSame('List all available fixtures.', $description);
    }
}
