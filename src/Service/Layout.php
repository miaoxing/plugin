<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\BaseService;

class Layout extends BaseService
{
    /**
     * @var bool
     */
    protected $header = true;

    /**
     * @var bool
     */
    protected $footer = true;

    /**
     * @return bool
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @return bool
     */
    public function getFooter()
    {
        return $this->footer;
    }

    /**
     * @return $this
     */
    public function showHeader()
    {
        $this->header = true;
        return $this;
    }

    /**
     * @return $this
     */
    public function hideHeader()
    {
        $this->header = false;
        return $this;
    }

    /**
     * @return $this
     */
    public function showFooter()
    {
        $this->footer = true;
        return $this;
    }

    /**
     * @return $this
     */
    public function hideFooter()
    {
        $this->footer = false;
        return $this;
    }
}
