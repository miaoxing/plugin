<?php

namespace Miaoxing\Plugin\Controller\Admin;

class Plugin extends \Miaoxing\Plugin\BaseController
{
    protected $guestPages = ['admin/plugin/refreshCache'];

    public function indexAction($req)
    {
        switch ($req['_format']) {
            case 'json':
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

            default:
                return [];
        }
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
