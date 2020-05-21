<?php

namespace Miaoxing\Plugin\Command;

class Install extends BaseCommand
{

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
            $this->ret($ret);
        }

        $this->suc('Install successfully');
    }
    protected function configure()
    {
        $this->setDescription('Install the application');
    }
}
