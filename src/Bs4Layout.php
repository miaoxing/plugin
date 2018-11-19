<?php

namespace Miaoxing\Plugin;

trait Bs4Layout
{
    public function __construct(array $options = [])
    {
        parent::__construct($options);

        $this->view->setDefaultLayout('@plugin/layouts/default-bs4.php');
    }
}
