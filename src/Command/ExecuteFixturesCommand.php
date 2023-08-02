<?php

declare(strict_types=1);

namespace Dot\DataFixtures\Command;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function sprintf;

use const DIRECTORY_SEPARATOR;

class ExecuteFixturesCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'fixtures:execute';

    private EntityManager $entityManager;

    private string $path;

    public function __construct(EntityManager $entityManager, string $path)
    {
        parent::__construct(self::$defaultName);

        $this->entityManager = $entityManager;
        $this->path          = $path;
    }

    protected function configure(): void
    {
        $this->setName(self::$defaultName)
            ->setDescription('Executes one or multiple fixtures.')
            ->addOption(
                'class',
                null,
                InputOption::VALUE_OPTIONAL,
                'Execute a specific fixture.',
                false
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $loader = new Loader();
        $purger = new ORMPurger($this->entityManager);

        $executor = new ORMExecutor($this->entityManager, $purger);

        if ($input->getOption('class') === false) {
            $loader->loadFromDirectory($this->path);
        } else {
            $loader->loadFromFile($this->path . DIRECTORY_SEPARATOR . $input->getOption('class') . '.php');
        }

        $fixtures = $loader->getFixtures();

        $executor->execute($fixtures, true);

        foreach ($fixtures as $fixture) {
            $output->writeln(sprintf('<info>Executing %s </info>', $fixture::class));
        }

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
}
