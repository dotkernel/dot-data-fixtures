<?php

declare(strict_types=1);

namespace Dot\DataFixtures\Command;

use DateTimeImmutable;
use Doctrine\Common\DataFixtures\Loader;
use ReflectionClass;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function filemtime;
use function is_string;
use function realpath;
use function str_replace;
use function str_starts_with;
use function strlen;
use function substr;

use const DIRECTORY_SEPARATOR;

#[AsCommand(name: self::NAME, description: self::DESCRIPTION)]
class ListFixturesCommand extends Command
{
    public const NAME        = 'fixtures:list';
    public const DESCRIPTION = 'List all available fixtures.';

    /**
     * Kept from 1.x for subclasses that read it. Symfony Console 7 and 8 no longer read it;
     * the name comes from #[AsCommand] and the constructor.
     */
    protected static string $defaultName = self::NAME;

    public function __construct(
        private readonly Loader $loader,
        private readonly string $path,
    ) {
        parent::__construct(self::$defaultName);
    }

    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription(self::DESCRIPTION);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->loader->loadFromDirectory($this->path);

        $directory = (string) realpath($this->path) . DIRECTORY_SEPARATOR;
        $rows      = [];
        foreach ($this->loader->getFixtures() as $fixture) {
            $reflectionClass = new ReflectionClass($fixture);
            $fileName        = $reflectionClass->getFileName();
            $command         = '';
            $lastUpdatedAt   = '';
            if (is_string($fileName)) {
                if (str_starts_with($fileName, $directory)) {
                    $relative = substr($fileName, strlen($directory), -strlen('.php'));
                    $relative = str_replace(DIRECTORY_SEPARATOR, '/', $relative);
                    $command  = ExecuteFixturesCommand::NAME . ' --class=' . $relative;
                }

                $modified = filemtime($fileName);
                if ($modified !== false) {
                    $lastUpdatedAt = (new DateTimeImmutable('@' . $modified))->format('Y-m-d H:i:s');
                }
            }

            $rows[] = [$reflectionClass->getName(), $command, $lastUpdatedAt];
        }

        (new Table($output))
            ->setHeaders(['Namespace', 'Run fixture command', 'Last updated at'])
            ->setRows($rows)
            ->render();

        return Command::SUCCESS;
    }
}
