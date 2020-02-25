<?php

namespace Miaoxing\Plugin;

class Plugin extends \Miaoxing\Plugin\BasePlugin
{
    protected $name = '插件核心';

    protected $description = '插件管理,包括插件获取,安装,卸载等功能';

    public function controllerInit()
    {
        // 服务需要提供给前端配置，如何处理？
    }
}
