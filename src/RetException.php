<?php

namespace Miaoxing\Plugin;

use Exception;

class RetException extends Exception
{
    protected $ret;

    public function __construct($ret)
    {
        $this->ret = $ret;
        parent::__construct($ret['message'], $ret['code']);
    }

    public function getRet()
    {
        return $this->ret;
    }
}
