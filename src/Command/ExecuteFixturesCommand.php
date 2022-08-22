<?php


namespace Dot\DataFixtures\Command;


use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

/**
 * Class ExecuteFixturesCommand
 * @package Dot\DataFixtures\Command
 */
class ExecuteFixturesCommand extends Command
{
    protected static $defaultName = 'fixtures:execute';

    private EntityManager $entityManager;

    private string $path;

    /**
     * ExecuteFixturesCommand constructor.
     * @param EntityManager $entityManager
     * @param string $path
     */
    public function __construct(EntityManager $entityManager, string $path)
    {
        parent::__construct(self::$defaultName);

        $this->entityManager = $entityManager;
        $this->path = $path;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName(self::$defaultName)
            ->setDescription('Executes one or multiple fixtures.')
            ->addOption(
                'append',
                null,
                InputOption::VALUE_OPTIONAL,
                'If true the data is appended else the table is emptied before inserting the data.',
                'true'
            )->addOption(
                'class',
                null,
                InputOption::VALUE_OPTIONAL,
                'Execute a specific fixture.',
                false
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $loader = new Loader();
        $purger = new ORMPurger();

        $executor = new ORMExecutor($this->entityManager, $purger);

        if ($input->getOption('class') === false) {
            $loader->loadFromDirectory($this->path);
        } else {
            $loader->loadFromFile($this->path . DIRECTORY_SEPARATOR . $input->getOption('class') . '.php');
        }

        $fixtures = $loader->getFixtures();

        $executor->execute($fixtures, $input->getOption('append') === 'true');

        foreach ($fixtures as $fixture) {
            $output->writeln(sprintf('<info>Executing %s </info>', get_class($fixture)));
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
