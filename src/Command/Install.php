<?php

namespace Miaoxing\Plugin\Command;

use Miaoxing\Services\Command\BaseCommand;

class Install extends BaseCommand
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the application';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $wei = wei();

        // 1. 初始化数据库
        $db = $wei->db;
        $db->executeUpdate('CREATE DATABASE IF NOT EXISTS ' . $db->getDbname());
        $db->useDb($db->getDbname());

        // 2. 执行迁移语句
        $wei->migration->setOutput($this->output)->migrate();

        // 3. 逐个安装插件
        foreach ($wei->plugin->getAll() as $plugin) {
            $ret = $wei->plugin->install($plugin->getId());
            $this->output->write($plugin->getId() . ': ');
            $this->writeRet($ret);
        }

        $this->info('Install successfully');
    }
}
