<?php

namespace Miaoxing\Plugin\Service;

/**
 * @property App $app
 */
class Url extends \Wei\Url
{
    public function curIndex($action = '', $argsOrParams = array(), $params = array())
    {
        return $this->__invoke($this->app->getController() . ($action ? ('/' . $action) : ''), $argsOrParams, $params);
    }

    public function curNew()
    {
        return $this->curIndex('new');
    }

    public function curEdit($id = null)
    {
        return $this->curIndex(($id || $this->request['id']) . '/edit');
    }

    public function curShow($id = null)
    {
        return $this->curIndex($id ?: $this->request['id']);
    }

    public function curDestroy($id = null)
    {
        return $this->curIndex(($id ?: $this->request['id']) . '/destroy');
    }

    public function curForm($id = null)
    {
        return $this->curIndex(($id ?: $this->request['id']) ? 'update' : 'create');
    }
}
