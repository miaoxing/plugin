<?php

namespace Miaoxing\Plugin\Controller\AdminApi;

use Miaoxing\Plugin\BaseController;

class PluginsController extends BaseController
{
    protected $controllerName = '插件';

    protected $actionPermissions = [
        'index' => '列表',
        'update' => '安装/卸载'
    ];

    protected $actionAuths = [
        'refreshCache' => false,
    ];

    public function indexAction($req)
    {
        $repo = wei()->plugin;
        $plugins = $repo->getAll();

        $data = [];
        foreach ($plugins as $plugin) {
            $data[] = $plugin->toArray() + [
                    'installed' => (string) $repo->isInstalled($plugin->getId()),
                ];
        }

        return $this->suc([
            'message' => '读取列表成功',
            'data' => $data,
            'records' => count($data),
        ]);
    }

    public function updateAction($req)
    {
        $pluginRepo = wei()->plugin;
        $plugin = $pluginRepo->getById($req['id']);
        if (!$plugin) {
            return $this->err(sprintf('插件"%s"不存在', $req['id']));
        }

        if (isset($req['installed'])) {
            if ($req['installed']) {
                $ret = $pluginRepo->install($plugin->getId());
            } else {
                $ret = $pluginRepo->uninstall($plugin->getId());
            }

            return $this->ret($ret);
        }

        return $this->suc();
    }

    /**
     * 刷新缓存
     */
    public function refreshCacheAction()
    {
        wei()->plugin->getEvents(true);

        return $this->suc('Refresh cache success !');
    }

    public function eventsAction()
    {
        return $this->suc([
            'data' => wei()->plugin->getEvents(),
        ]);
    }
}
