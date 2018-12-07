<?php

namespace Miaoxing\Plugin\Service;

class Request extends \Wei\Request
{
    public function __construct(array $options = array())
    {
        parent::__construct($options);

        if (strpos($this->getServer('HTTP_CONTENT_TYPE'), 'application/json') !== false) {
            $this->data += (array) json_decode($this->getContent(), true);
        }
    }

    public function json()
    {
        return $this->acceptJson();
    }

    public function csv()
    {
        return $this['_format'] == 'csv';
    }
}
