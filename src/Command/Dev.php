<?php

namespace Miaoxing\Plugin\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;

class Dev extends BaseCommand
{
    protected function configure()
    {
        $this->setDescription('Start a PHP development server')
            ->addOption('port', null, InputOption::VALUE_OPTIONAL, 'The port of dev server', 8888)
            ->addOption('host', null, InputOption::VALUE_OPTIONAL, 'The host of dev server', 'localhost');
    }

    protected function handle()
    {
        $process = $this->start();

        // Monitor the .env file for changes
        $envFilePath = '.env';
        $lastModifiedTime = filemtime($envFilePath);
        while ($process->isRunning()) {
            clearstatcache();
            $currentModifiedTime = filemtime($envFilePath);
            if ($currentModifiedTime > $lastModifiedTime) {
                $this->output->writeln('.env file changed. Restarting server...');
                $process->stop();
                $process = $this->start();
                $lastModifiedTime = $currentModifiedTime;
            }
            sleep(1);
        }
    }

    protected function start(): Process
    {
        $port = $this->getOption('port');
        $host = $this->getOption('host');
        $documentRoot = 'public';

        $url = $host . ':' . $port;
        $this->output->writeln("\nStarting PHP server on <info>http://$url</info>\n");

        $process = new Process(['php', '-S', $url, '-t', $documentRoot]);
        $process->setTty(true);
        $process->start(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        $this->output->writeln('Listening for changes in .env file...');

        return $process;
    }
}