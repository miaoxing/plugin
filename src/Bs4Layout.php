<?php

namespace Miaoxing\Plugin;

use Miaoxing\Plugin\Service\App;
use Miaoxing\Plugin\Service\View;

/**
 * @property View $view
 * @property App $app
 */
trait Bs4Layout
{
    public function __construct(array $options = [])
    {
        parent::__construct($options);

        if ($this->app->isAdmin()) {
            $this->view->setDefaultLayout('@admin/admin/layout-bs4.php');
        } else {
            $this->view->setDefaultLayout('@plugin/layouts/default-bs4.php');
        }
    }
}
