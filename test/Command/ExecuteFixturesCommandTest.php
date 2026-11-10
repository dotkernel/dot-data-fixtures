<?php

declare(strict_types=1);

namespace DotTest\DataFixtures\Command;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\ORM\EntityManager;
use Dot\DataFixtures\Command\ExecuteFixturesCommand;
use DotTest\DataFixtures\Fixtures\Data\RoleFixture;
use DotTest\DataFixtures\Fixtures\Data\Sub\NestedFixture;
use DotTest\DataFixtures\Fixtures\EntityManagerFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Tester\CommandTester;

use function get_included_files;
use function realpath;

#[CoversClass(ExecuteFixturesCommand::class)]
class ExecuteFixturesCommandTest extends TestCase
{
    private EntityManager $entityManager;
    private ExecuteFixturesCommand $command;

    protected function setUp(): void
    {
        $this->entityManager = EntityManagerFactory::create();
        $this->command       = new ExecuteFixturesCommand(
            new Loader(),
            new ORMExecutor($this->entityManager),
            EntityManagerFactory::FIXTURES_PATH
        );
    }

    public function testNameAndDescriptionComeFromAttribute(): void
    {
        $attributes = (new ReflectionClass(ExecuteFixturesCommand::class))->getAttributes(AsCommand::class);

        $this->assertCount(1, $attributes);
        $this->assertSame(ExecuteFixturesCommand::NAME, $attributes[0]->newInstance()->name);
        $this->assertSame('fixtures:execute', $this->command->getName());
        $this->assertSame('Executes one or multiple fixtures.', $this->command->getDescription());
    }

    public function testClassOptionRequiresValueAndHasNoDefault(): void
    {
        $option = $this->command->getDefinition()->getOption('class');

        $this->assertTrue($option->isValueRequired());
        $this->assertNull($option->getShortcut());
        $this->assertNull($option->getDefault());
    }

    public function testExecuteAllInsertsEveryFixture(): void
    {
        $tester = new CommandTester($this->command);

        $this->assertSame(Command::SUCCESS, $tester->execute([]));
        $this->assertEqualsCanonicalizing(
            ['admin', 'nested'],
            EntityManagerFactory::roleNames($this->entityManager)
        );
        $this->assertStringContainsString('Executing ' . RoleFixture::class, $tester->getDisplay());
        $this->assertStringContainsString('Executing ' . NestedFixture::class, $tester->getDisplay());
        $this->assertStringContainsString('Fixtures have been loaded.', $tester->getDisplay());
    }

    public function testExecuteOneInsertsOnlyThatFixture(): void
    {
        $tester = new CommandTester($this->command);

        $this->assertSame(Command::SUCCESS, $tester->execute(['--class' => 'RoleFixture']));
        $this->assertSame(['admin'], EntityManagerFactory::roleNames($this->entityManager));
    }

    public function testExecuteNestedFixtureByRelativePath(): void
    {
        $tester = new CommandTester($this->command);

        $this->assertSame(Command::SUCCESS, $tester->execute(['--class' => 'Sub/NestedFixture']));
        $this->assertSame(['nested'], EntityManagerFactory::roleNames($this->entityManager));
    }

    public function testExecuteAppendsOnRepeatedRuns(): void
    {
        $tester = new CommandTester($this->command);
        $tester->execute(['--class' => 'RoleFixture']);
        $tester->execute(['--class' => 'RoleFixture']);

        $this->assertSame(['admin', 'admin'], EntityManagerFactory::roleNames($this->entityManager));
    }

    public function testClassOptionWithoutValueIsRejected(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The "--class" option requires a value.');

        $this->command->run(new ArgvInput(['bin/doctrine', '--class']), new BufferedOutput());
    }

    public function testMissingFixtureReturnsFailure(): void
    {
        $tester = new CommandTester($this->command);

        $this->assertSame(Command::FAILURE, $tester->execute(['--class' => 'MissingFixture']));
        $this->assertStringContainsString('Fixture "MissingFixture" was not found', $tester->getDisplay());
        $this->assertSame([], EntityManagerFactory::roleNames($this->entityManager));
    }

    public function testFixtureOutsideFixturesDirectoryIsRefused(): void
    {
        $outside = realpath(__DIR__ . '/../Fixtures/Outside/OutsideFixture.php');
        $tester  = new CommandTester($this->command);

        $this->assertSame(Command::FAILURE, $tester->execute(['--class' => '../Outside/OutsideFixture']));
        $this->assertStringContainsString('was not found', $tester->getDisplay());
        $this->assertSame([], EntityManagerFactory::roleNames($this->entityManager));
        $this->assertNotContains($outside, get_included_files());
    }
}
