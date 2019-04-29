<?php

namespace Miaoxing\Plugin\Controller\Cli;

use Miaoxing\Plugin\BaseController;
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

        if (!is_dir('plugins')) {
            mkdir('plugins');
            $this->writeln(sprintf('Create %s directory ', $cli->success('plugins')));
        }

        $plugins = $this->plugin->getAll();
        foreach ($plugins as $plugin) {
            $path = $plugin->getBasePath();

            $source = $path . '/public';
            if (!is_dir($source)) {
                continue;
            }

            $id = $plugin->getId();
            $target = 'public/plugins/' . $id;
            if (is_dir($target)) {
                $this->writeln(sprintf('Remove %s', $cli->success($target)));
                $this->remove($target);
            }

            $source = '../../' . $source;
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
