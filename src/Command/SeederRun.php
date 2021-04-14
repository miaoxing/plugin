<?php

namespace Miaoxing\Plugin\Command;

use Miaoxing\Plugin\Service\Seeder;

class SeederRun extends BaseCommand
{
    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        Seeder::setOutput($this->output)->run();
    }

    protected function configure()
    {
        $this->setDescription('Run the seeders')
            ->setAliases(['seed']);
    }
}
