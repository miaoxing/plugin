<?php

namespace MiaoxingTest\Plugin\Fixture\Job;

use Miaoxing\Plugin\Queue\BaseJob;

/**
 * @mixin \ArrayCachePropMixin
 */
class TestRelease extends BaseJob
{
    public function __invoke(): void
    {
        if (!$this->arrayCache->get('__release')) {
            $this->arrayCache->set('__release', 0);
        }

        $this->arrayCache->incr('__release');

        // Simulating the first two external calls failed
        if ($this->arrayCache->get('__release') <= 2) {
            $this->release();
        }
    }
}
