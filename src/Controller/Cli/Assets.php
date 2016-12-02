<?php

namespace Miaoxing\Plugin\Controller\Cli;

use miaoxing\plugin\BaseController;
use Miaoxing\Plugin\Service\Cli;

/**
 * @property Cli $cli
 */
class Assets extends BaseController
{
    /**
     * 发布素材到开放的目录
     */
    public function publishAction()
    {
        $cli = $this->cli;

        $plugins = $this->plugin->getAll();
        foreach ($plugins as $plugin) {
            $path = $plugin->getBasePath();

            // TODO V2 只处理vendor下的插件,直到迁移完毕
            if (strpos($path, 'vendor/') !== 0) {
                continue;
            }

            $source = $path . '/public';
            if (!is_dir($source)) {
                continue;
            }

            $id = $plugin->getId();
            $target = 'plugins/' . $id;
            if (is_dir($target)) {
                $this->writeln(sprintf('Remove %s', $cli->success($target)));
                $this->remove($target);
            }

            $source = '../' . $source;
            $this->writeln(sprintf('Symlink %s to %s', $cli->success($source), $cli->success($target)));
            symlink($source, $target);
        }

        return $this->suc();
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

    /**
     * @param string $message
     */
    protected function writeln($message)
    {
        fwrite(STDERR, $message . "\n");
    }
}
