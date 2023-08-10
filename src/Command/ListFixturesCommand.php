<?php

declare(strict_types=1);

namespace Dot\DataFixtures\Command;

use DateTimeImmutable;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use ReflectionClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function filemtime;

class ListFixturesCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'fixtures:list';

    protected Loader $loader;
    protected ORMPurger $purger;
    protected ORMExecutor $executor;
    private string $path;

    public function __construct(Loader $loader, ORMPurger $purger, ORMExecutor $executor, string $path)
    {
        parent::__construct(self::$defaultName);

        $this->loader   = $loader;
        $this->purger   = $purger;
        $this->executor = $executor;
        $this->path     = $path;
    }

    protected function configure(): void
    {
        $this->setName(self::$defaultName)->setDescription('List all available fixtures.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->loader->loadFromDirectory($this->path);

        $rows        = [];
        $commandName = ExecuteFixturesCommand::getDefaultName();

        foreach ($this->loader->getFixtures() as $fixture) {
            $reflectionClass = new ReflectionClass($fixture);
            $lastUpdatedAt   = DateTimeImmutable::createFromFormat(
                'U',
                (string) filemtime($reflectionClass->getFileName())
            );

            $rows[] = [
                'namespace'       => $reflectionClass->getName(),
                'command'         => $commandName . ' --class=' . $reflectionClass->getShortName(),
                'last_updated_at' => $lastUpdatedAt->format('Y-m-d H:i:s'),
            ];
        }

        $table = new Table($output);
        $table
            ->setHeaders(['Namespace', 'Run fixture command', 'Last updated at'])
            ->setRows($rows)
            ->render();

        return Command::SUCCESS;
    }
}
