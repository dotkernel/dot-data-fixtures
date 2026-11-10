<?php

declare(strict_types=1);

namespace Dot\DataFixtures\Command;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function is_string;
use function realpath;
use function sprintf;
use function str_starts_with;

use const DIRECTORY_SEPARATOR;

#[AsCommand(name: self::NAME, description: 'Executes one or multiple fixtures.')]
class ExecuteFixturesCommand extends Command
{
    public const NAME = 'fixtures:execute';

    public function __construct(
        private readonly Loader $loader,
        private readonly ORMExecutor $executor,
        private readonly string $path,
    ) {
        parent::__construct(self::NAME);
    }

    protected function configure(): void
    {
        $this->addOption(
            'class',
            null,
            InputOption::VALUE_REQUIRED,
            'Execute a specific fixture, given by its path relative to the fixtures directory, without ".php".'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $class = $input->getOption('class');

        try {
            if (is_string($class)) {
                $this->loader->loadFromFile($this->resolveFixtureFile($class));
            } else {
                $this->loader->loadFromDirectory($this->path);
            }
        } catch (InvalidArgumentException $exception) {
            $output->writeln(sprintf('<error>%s</error>', $exception->getMessage()));

            return Command::FAILURE;
        }

        $fixtures = $this->loader->getFixtures();
        foreach ($fixtures as $fixture) {
            $output->writeln(sprintf('<info>Executing %s</info>', $fixture::class));
        }

        $this->executor->execute($fixtures, true);

        $output->writeln("<info>Fixtures have been loaded.</info>");
        $output->write("<info>                .''
      ._.-.___.' (`\
     //(        ( `'
    '/ )\ ).__. )
    ' <' `\ ._/'\
       `   \     \
</info>");

        return Command::SUCCESS;
    }

    /**
     * Resolves a fixture name relative to the fixtures directory and refuses any file outside it.
     *
     * @throws InvalidArgumentException
     */
    private function resolveFixtureFile(string $class): string
    {
        $directory = realpath($this->path);
        $file      = realpath($this->path . DIRECTORY_SEPARATOR . $class . '.php');
        if (
            ! is_string($directory)
            || ! is_string($file)
            || ! str_starts_with($file, $directory . DIRECTORY_SEPARATOR)
        ) {
            throw new InvalidArgumentException(
                sprintf('Fixture "%s" was not found in "%s".', $class, $this->path)
            );
        }

        return $file;
    }
}
