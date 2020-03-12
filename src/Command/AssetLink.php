<?php

namespace Miaoxing\Plugin\Command;

class AssetLink extends BaseCommand
{
    /**
     * 发布素材到开放的目录
     */
    public function handle()
    {
        $basePath = 'public/plugins';

        if (!is_dir($basePath)) {
            mkdir($basePath);
            $this->suc(sprintf('Create %s directory ', $basePath));
        }

        $plugins = $this->plugin->getAll();
        foreach ($plugins as $plugin) {
            $path = $plugin->getBasePath();

            $source = $path . '/public';
            if (!is_dir($source)) {
                continue;
            }

            $id = $plugin->getId();
            $target = $basePath . '/' . $id;
            if (is_dir($target)) {
                $this->suc(sprintf('Remove %s', $target));
                $this->remove($target);
            }

            $source = '../../' . $source;
            $this->suc(sprintf('Symlink %s to %s', $source, $target));
            symlink($source, $target);
        }

        return $this->suc('创建成功');
    }

    /**
     * @param string $target
     */
    protected function remove($target)
    {
        if (is_link($target)) {
            unlink($target);
        } elseif (is_dir($target)) {
            rmdir($target);
        }
    }
}
