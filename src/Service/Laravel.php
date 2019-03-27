<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\BaseService;

/**
 * @todo 成熟后作为一个插件？
 */
class Laravel extends BaseService
{
    public function bootstrap()
    {
        require 'laravel/bootstrap.php';
    }
}
