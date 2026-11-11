<?php

declare(strict_types=1);

namespace DotTest\DataFixtures\Command;

use Doctrine\Common\DataFixtures\Loader;
use Dot\DataFixtures\Command\ListFixturesCommand;
use DotTest\DataFixtures\Fixtures\Data\RoleFixture;
use DotTest\DataFixtures\Fixtures\Data\Sub\NestedFixture;
use DotTest\DataFixtures\Fixtures\EntityManagerFactory;
use DotTest\DataFixtures\Fixtures\Legacy\LegacyListFixturesCommand;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

#[CoversClass(ListFixturesCommand::class)]
class ListFixturesCommandTest extends TestCase
{
    public function testNameAndDescriptionComeFromAttribute(): void
    {
        $command    = new ListFixturesCommand(new Loader(), EntityManagerFactory::FIXTURES_PATH);
        $attributes = (new ReflectionClass(ListFixturesCommand::class))->getAttributes(AsCommand::class);

        $this->assertCount(1, $attributes);
        $this->assertSame(ListFixturesCommand::NAME, $attributes[0]->newInstance()->name);
        $this->assertSame('fixtures:list', $command->getName());
        $this->assertSame('List all available fixtures.', $command->getDescription());
    }

    public function testDefaultNamePropertyKeepsItsVersion1Shape(): void
    {
        $property = new ReflectionProperty(ListFixturesCommand::class, 'defaultName');

        $this->assertTrue($property->isProtected());
        $this->assertTrue($property->isStatic());
        $this->assertFalse($property->hasType());
        $this->assertSame(ListFixturesCommand::NAME, $property->getValue());
    }

    public function testVersion1SubclassRedeclaringDefaultNameStillWorks(): void
    {
        $command = new LegacyListFixturesCommand(new Loader(), EntityManagerFactory::FIXTURES_PATH);

        $this->assertSame(
            'app:fixtures:list',
            (new ReflectionProperty(LegacyListFixturesCommand::class, 'defaultName'))->getValue()
        );
        // As in 1.x, the parent uses self::$defaultName, so the command keeps its name.
        $this->assertSame(ListFixturesCommand::NAME, $command->getName());
        $this->assertSame(ListFixturesCommand::DESCRIPTION, $command->getDescription());
        $this->assertSame(Command::SUCCESS, (new CommandTester($command))->execute([]));
    }

    public function testListsEveryFixtureWithARunnableCommand(): void
    {
        $tester = new CommandTester(new ListFixturesCommand(new Loader(), EntityManagerFactory::FIXTURES_PATH));

        $this->assertSame(Command::SUCCESS, $tester->execute([]));

        $display = $tester->getDisplay();
        $this->assertStringContainsString(RoleFixture::class, $display);
        $this->assertStringContainsString(NestedFixture::class, $display);
        $this->assertStringContainsString('fixtures:execute --class=RoleFixture ', $display);
        $this->assertStringContainsString('fixtures:execute --class=Sub/NestedFixture ', $display);
        $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $display);
    }

    public function testMissingFixturesDirectoryThrows(): void
    {
        $tester = new CommandTester(new ListFixturesCommand(new Loader(), __DIR__ . '/does-not-exist'));

        $this->expectException(InvalidArgumentException::class);
        $tester->execute([]);
    }
}
