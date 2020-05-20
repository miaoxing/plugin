<?php

namespace Miaoxing\Plugin\Service;

use Wei\Wei;

class Session extends \Wei\Session
{
    public function __construct(array $options = [])
    {
        if (!isset($options['namespace'])) {
            /** @var Wei $wei */
            $wei = $options['wei'];
            $options['namespace'] = $wei->app->getNamespace();
        }

        parent::__construct($options);
    }
}
