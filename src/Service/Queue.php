<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Queue\Service\DbQueue;

class Queue extends DbQueue
{
    public function __construct(array $options = array())
    {
        parent::__construct($options);

        $this->default = wei()->app->getNamespace();
    }
}
