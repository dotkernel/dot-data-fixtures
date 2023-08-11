<?php

declare(strict_types=1);

namespace DotTest\DataFixtures\Command;

use Doctrine\Common\DataFixtures\Loader;
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
        $loader  = $this->createMock(Loader::class);
        $path    = getcwd() . '/data/doctrine/fixtures';
        $command = new ListFixturesCommand($loader, $path);
        $this->assertInstanceOf(ListFixturesCommand::class, $command);
    }

    /**
     * @throws Exception
     * @throws ReflectionException
     */
    public function testWillExecuteCommand(): void
    {
        $loader = $this->createMock(Loader::class);
        $loader->method('getFixtures')->willReturnMap([
            [
                [],
            ],
        ]);
        $path = getcwd() . '/data/doctrine/fixtures';

        $command    = new ListFixturesCommand($loader, $path);
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
        $path        = getcwd() . '/data/doctrine/fixtures';
        $command     = new ListFixturesCommand($loader, $path);
        $defaultName = $command->getName();
        $description = $command->getDescription();
        $this->assertSame('fixtures:list', $defaultName);
        $this->assertSame('List all available fixtures.', $description);
    }
}
