<?php

namespace Miaoxing\Plugin\Queue;

class UpdateSomething extends BaseJob
{
    public function __invoke($data)
    {
        $this->logger->debug('ddd');
    }
}
