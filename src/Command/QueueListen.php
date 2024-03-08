<?php

namespace Miaoxing\Plugin\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

class QueueListen extends BaseCommand
{
    protected function handle()
    {
        $phpBinaryFinder = new PhpExecutableFinder();
        $phpBinaryPath = $phpBinaryFinder->find();

        $process = new Process([
            $phpBinaryPath,
            'wei.php',
            'queue:run',
            $this->getArgument('name'),
        ]);

        $this->output->writeln('Running queue:run');
        while (true) {
            $process->run(function ($type, $buffer) {
                $this->output->writeln($buffer);
            });

            $memory = memory_get_usage(true) / 1024 / 1024;
            $this->output->writeln('Memory usage: ' . $memory . 'M', OutputInterface::VERBOSITY_VERBOSE);
            if ($memory >= (int) $this->getOption('memory')) {
                $this->output->writeln(sprintf('Memory limit exceeded %s, stop listener', $this->getOption('memory')));
                break;
            }
        }
    }

    protected function configure()
    {
        $this->setDescription('Run the queue listener')
            ->addArgument('name', InputArgument::OPTIONAL, 'The name of queue')
            ->addOption('memory', 'm', InputArgument::OPTIONAL, 'The memory limit in megabytes', 128);
    }
}
