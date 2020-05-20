<?php

namespace Basilicom\PimcorePluginMigrationToolkit\Command;

use Pimcore;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class MigrateInSeparateProcessesCommand extends AbstractCommand
{
    const LOG_EMPTY_LINE     = '                                                            ';
    const LOG_SEPARATOR_LINE = '============================================================';

    protected static $defaultName = 'basilicom:migrations:migrate-in-separate-processes';

    protected function configure()
    {
        $this->setDescription('Executes the same migrations as the pimcore:migrations:migrate command, '.
            'but each one is run in a separate process, to prevent problems with PHP classes that changed during the runtime');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // The following prevents problems when the container changes during runtime - which is the case with migrations
        $eventDispatcher = Pimcore::getKernel()->getContainer()->get('event_dispatcher');
        foreach ($eventDispatcher->getListeners(ConsoleEvents::TERMINATE) as $listener) {
            $eventDispatcher->removeListener(ConsoleEvents::TERMINATE, $listener);
        }

        $unexecutedMigrations = $this->getUnexecutedMigrations();

        if (count($unexecutedMigrations) < 1) {
            $output->writeln('No migrations to execute');
            exit(0);
        }

        $output->writeln('Following migrations will be executed: ' . PHP_EOL . implode(PHP_EOL, $unexecutedMigrations));

        foreach ($unexecutedMigrations as $migration) {
            $output->writeln(self::LOG_EMPTY_LINE);
            $output->writeln(self::LOG_SEPARATOR_LINE);
            $output->writeln('                Executing the migration ' . $migration);
            $output->writeln(self::LOG_SEPARATOR_LINE);

            $process = new Process(
                ['bin/console', 'pimcore:migrations:execute', $migration, '--no-interaction'],
                PIMCORE_PROJECT_ROOT
            );
            $process->setTimeout(120);
            $process->run(function ($type, $buffer) use ($output) {
                if (Process::ERR === $type) {
                    $output->writeln('<error>' . $buffer . '</error>');
                } else {
                    $output->writeln($buffer);
                }
            });

            if ($process->getExitCode() !== 0) {
                exit(1);
            }
        }

        $output->writeln(self::LOG_EMPTY_LINE);
        $output->writeln(self::LOG_SEPARATOR_LINE);
        $output->writeln('                    Migrations finished                     ');
        $output->writeln(self::LOG_SEPARATOR_LINE);
        $output->writeln(self::LOG_EMPTY_LINE);
    }

    protected function getUnexecutedMigrations()
    {
        $process = new Process(
            ['bin/console pimcore:migrations:status --show-versions --no-interaction | grep "not migrated" | awk \'{print substr($4, 2, length($4) - 2) }\''],
            PIMCORE_PROJECT_ROOT
        );

        $process->run();

        $unexecutedMigrations = [];
        foreach ($process as $type => $psOutputLine) {
            if ($type === 'out') {
                $unexecutedMigrations = explode(PHP_EOL, $psOutputLine);
            }
        }

        return array_filter($unexecutedMigrations);
    }
}
