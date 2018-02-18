<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\BaseService;
use Wei\Block;

/**
 * @property-read Block $block
 */
class Layout extends BaseService
{
    /**
     * @var bool
     */
    protected $header = true;

    /**
     * @var string
     */
    protected $headerTitle = '';

    /**
     * @var bool
     */
    protected $footer = true;

    protected $jsFiles = [];

    protected $cssFiles = [];

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

    /**
     * @param string $title
     * @return $this
     */
    public function setHeaderTitle($title)
    {
        $this->headerTitle = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getHeaderTitle()
    {
        return $this->headerTitle;
    }

    public function addJs($src)
    {
        $this->jsFiles[] = $src;

        return $this;
    }

    public function addCss($href)
    {
        $this->cssFiles[] = $href;

        return $this;
    }

    public function renderHead()
    {
        $this->event->trigger('style');
        echo $this->block->get('css');
    }

    public function renderFooter()
    {
        $this->event->trigger('script');
        echo $this->block->get('js');
        $this->event->trigger('bodyEnd');
    }
}
