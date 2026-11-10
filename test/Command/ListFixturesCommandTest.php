<?php

declare(strict_types=1);

namespace DotTest\DataFixtures\Command;

use Doctrine\Common\DataFixtures\Loader;
use Dot\DataFixtures\Command\ListFixturesCommand;
use DotTest\DataFixtures\Fixtures\Data\RoleFixture;
use DotTest\DataFixtures\Fixtures\Data\Sub\NestedFixture;
use DotTest\DataFixtures\Fixtures\EntityManagerFactory;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
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
