<?php

namespace Miaoxing\Plugin\Command;

use Miaoxing\Plugin\Service\Seeder;
use Symfony\Component\Console\Input\InputArgument;

class SeederRun extends BaseCommand
{
    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        Seeder::setOutput($this->output)->run([
            'name' => $this->getArgument('name'),
        ]);
    }

    protected function configure()
    {
        $this->setDescription('Run the seeders')
            ->setAliases(['seed'])
            ->addArgument('name', InputArgument::OPTIONAL, 'The name of the seeder');
    }
}
