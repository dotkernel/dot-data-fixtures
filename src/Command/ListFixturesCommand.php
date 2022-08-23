<?php


namespace Dot\DataFixtures\Command;


use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ReflectionClass;
use DateTimeImmutable;

/**
 * Class ListFixturesCommand
 * @package Dot\DataFixtures\Command
 */
class ListFixturesCommand extends Command
{
    protected static $defaultName = 'fixtures:list';

    private string $path;

    /**
     * ListFixturesCommand constructor.
     * @param string $path
     */
    public function __construct(string $path)
    {
        parent::__construct(self::$defaultName);

        $this->path = $path;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName(self::$defaultName)->setDescription('List all available fixtures.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $loader = new Loader();
        $loader->loadFromDirectory($this->path);

        $rows = [];
        foreach ($loader->getFixtures() as $fixture) {
            $reflectionClass = new ReflectionClass($fixture);
            $lastUpdatedAt = DateTimeImmutable::createFromFormat('U', filemtime($reflectionClass->getFileName()));

            $rows[] = [
                'namespace' => $reflectionClass->getName(),
                'command' => ExecuteFixturesCommand::getDefaultName() . ' --class=' . $reflectionClass->getShortName(),
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
