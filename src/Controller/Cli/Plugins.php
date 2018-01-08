<?php

namespace Miaoxing\Plugin\Controller\Cli;

use Miaoxing\Plugin\BaseController;

/**
 * 插件
 */
class Plugins extends BaseController
{
    public function refreshAction()
    {
        $this->plugin->getConfig(true);

        return $this->suc();
    }
}
