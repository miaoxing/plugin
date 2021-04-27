<?php

namespace Miaoxing\Plugin\Service;

use Wei\Req;

/**
 * @experimental Will merge to \Wei\Req
 */
class ObjectReq extends Req
{
    /**
     * {@inheritDoc}
     */
    public function __construct(array $options = [])
    {
        parent::__construct($options);

        if (false !== strpos($this->getServer('HTTP_CONTENT_TYPE'), 'application/json')) {
            $this->data = (array) json_decode($this->getContent()) + $this->data;
        }
    }
}
