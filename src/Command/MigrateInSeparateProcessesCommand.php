<?php

namespace Basilicom\PimcorePluginMigrationToolkit\Command;

use Basilicom\PimcorePluginMigrationToolkit\Trait\ClearCacheTrait;
use Pimcore;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class MigrateInSeparateProcessesCommand extends AbstractCommand
{
    use ClearCacheTrait;

    const LOG_EMPTY_LINE     = '                                                            ';
    const LOG_SEPARATOR_LINE = '======================================================================================';

    protected static $defaultName = 'basilicom:migrations:migrate-in-separate-processes';

    protected function configure()
    {
        $this->setDescription(
            'Executes the same migrations as the pimcore:migrations:migrate command, ' .
            'but each one is run in a separate process, to prevent problems with PHP classes that changed during the runtime.'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->clearCache();

        // The following prevents problems when the container changes during runtime - which is the case with migrations
        $eventDispatcher = Pimcore::getEventDispatcher();
        foreach ($eventDispatcher->getListeners(ConsoleEvents::TERMINATE) as $listener) {
            $eventDispatcher->removeListener(ConsoleEvents::TERMINATE, $listener);
        }

        $unexecutedMigrations = $this->getUnexecutedMigrations();

        if (count($unexecutedMigrations) < 1) {
            $output->writeln('<info>No migrations to execute</info>');
            exit(0);
        }

        $output->writeln('Following migrations will be executed: ' . PHP_EOL . implode(PHP_EOL, $unexecutedMigrations));

        foreach ($unexecutedMigrations as $migration) {
            $migrationVersion = substr($migration, strrpos($migration, '\\') + 1);
            $migrationPrefix  = substr($migration, 0, strrpos($migration, '\\'));
            $output->writeln(self::LOG_EMPTY_LINE);
            $output->writeln(self::LOG_SEPARATOR_LINE);
            $output->writeln('        Executing the migration ' . $migrationVersion . ' (' . $migrationPrefix . ')');
            $output->writeln(self::LOG_SEPARATOR_LINE);

            $process = new Process(
                ['bin/console', '--no-interaction', 'doctrine:migrations:execute', $migration],
                PIMCORE_PROJECT_ROOT
            );
            $process->setTimeout(120);
            $process->run(
                function ($type, $buffer) use ($output) {
                    if (Process::ERR === $type) {
                        $output->writeln('<error>' . $buffer . '</error>');
                    } else {
                        $output->writeln($buffer);
                    }
                }
            );

            if ($process->getExitCode() !== 0) {
                exit(1);
            }
        }

        $output->writeln(self::LOG_EMPTY_LINE);
        $output->writeln(self::LOG_SEPARATOR_LINE);
        $output->writeln('        Migrations finished');
        $output->writeln(self::LOG_SEPARATOR_LINE);
        $output->writeln(self::LOG_EMPTY_LINE);

        return 0;
    }

    protected function getUnexecutedMigrations()
    {
        $process = Process::fromShellCommandline(
            'bin/console doctrine:migrations:list | grep "not migrated" | cut -d"|" -f2 | awk \'{$1=$1};1\'',
            PIMCORE_PROJECT_ROOT
        );

        $process->start();

        $unexecutedMigrations = [];
        foreach ($process as $type => $outputLine) {
            if ($type === 'out') {
                $unexecutedMigrations = explode(PHP_EOL, trim($outputLine));
            }
        }

        return array_filter($unexecutedMigrations);
    }
}
