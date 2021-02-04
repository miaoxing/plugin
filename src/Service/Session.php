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
            // NOTE: namespace cant be int, why?
            // @internal namespace may be change in the future
            $options['namespace'] = 'miaoxing-' . $wei->app->getId();
        }

        parent::__construct($options);
    }
}
