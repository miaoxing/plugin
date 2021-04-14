<?php

namespace Miaoxing\Plugin\Command;

use Miaoxing\Plugin\Service\Seeder;

class SeederStatus extends BaseCommand
{
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        Seeder::setOutput($this->output)->status();
    }

    protected function configure()
    {
        $this->setDescription('Output the seeder status table');
    }
}
