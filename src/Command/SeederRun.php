<?php

namespace Miaoxing\Plugin\Command;

use Miaoxing\Plugin\Service\Seeder;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class SeederRun extends BaseCommand
{
    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        if ($this->getArgument('name') && $this->getOption('from')) {
            return $this->err('--from option cannot use with seeder name');
        }

        Seeder::setOutput($this->output)->run([
            'name' => $this->getArgument('name'),
            'from' => $this->getOption('from'),
        ]);
    }

    protected function configure()
    {
        $this->setDescription('Run the seeders')
            ->setAliases(['seed'])
            ->addArgument('name', InputArgument::OPTIONAL, 'The name of the seeder')
            ->addOption(
                'from',
                null,
                InputOption::VALUE_REQUIRED,
                'From which seeder to run, specify "root" to run all seeders'
            );
    }
}
