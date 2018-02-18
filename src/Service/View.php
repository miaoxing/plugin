<?php

namespace Miaoxing\Plugin\Service;

class View extends \Wei\View
{
    /**
     * 自动加载为视图变量的服务
     *
     * @var array
     */
    protected $autoloadServices = [
        'app',
        'e',
        'block',
        'url',
        'asset',
        'event',
        'curUser'
    ];

    /**
     * 是否已触发了自动加载
     *
     * @var bool
     */
    protected $autoloaded = false;

    /**
     * {@inheritdoc}
     */
    public function render($name, $data = array())
    {
        if (!$this->autoloaded) {
            $this->autoloaded = true;
            foreach ($this->autoloadServices as $service) {
                $this->data[$service] = $this->wei->get($service);
            }
        }

        return parent::render($name, $data);
    }
}
