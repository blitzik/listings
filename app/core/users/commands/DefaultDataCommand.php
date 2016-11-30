<?php

namespace Users\Commands;

use Symfony\Component\Console\Question\ConfirmationQuestion;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Component\Console\Command\Command;
use Doctrine\Common\DataFixtures\Loader;
use Kdyby\Doctrine\EntityManager;

class DefaultDataCommand extends Command
{
    /** @var EntityManager */
    private $em;


    public function __construct(EntityManager $entityManager)
    {
        parent::__construct();

        $this->em = $entityManager;
    }


    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('users:default-data')
             ->setDescription('Loads default users into database');
    }


    /**
     * Executes the current command.
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @param InputInterface $input An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|int null or 0 if everything went fine, or an error code
     *
     * @throws LogicException When this abstract method is not implemented
     *
     * @see setCode()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion(
            'All data in User table will be purged before inserting default data.
             Are you sure you want to continue?', false);

        if (!$helper->ask($input, $output, $question)) {
            return;
        }

        try{
            $loader = new Loader();
            $loader->loadFromDirectory(__DIR__ . '/../fixtures');
            $fixtures = $loader->getFixtures();

            $purger = new ORMPurger($this->em);

            $executor = new ORMExecutor($this->em, $purger);
            $executor->setLogger(function ($message) use ($output) {
                $output->writeln(sprintf('  <comment>></comment> <info>%s</info>', $message));
            });

            $executor->execute($fixtures);

            $output->writeln('Default users have been successfully loaded!');
            return 0;

        } catch (\Exception $e) {
            $output->writeLn("That's bad. An Error occurred: <error>{$e->getMessage()}</error>");
            return 1;
        }
    }


}