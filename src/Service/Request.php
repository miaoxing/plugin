<?php

namespace Miaoxing\Plugin\Service;

class Request extends \Wei\Request
{
    public function json()
    {
        return $this->acceptJson();
    }

    public function csv()
    {
        return $this['_format'] == 'csv';
    }
}
