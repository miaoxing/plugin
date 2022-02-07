<?php

namespace Miaoxing\Plugin\Service;

use Wei\Ret;

/**
 * @mixin \FsMixin
 * @mixin \ReqMixin
 */
class LocalStorage extends BaseStorage
{
    /**
     * {@inheritdoc}
     * @svc
     */
    protected function write(string $path, string $content, array $options = []): Ret
    {
        $this->fs->ensureDir(dirname($path));
        file_put_contents($path, $content);
        return Ret::suc();
    }

    /**
     * {@inheritdoc}
     * @svc
     */
    protected function getUrl(string $path): string
    {
        $publicDir = 'public';
        $length = strlen($publicDir) + 1;

        if (substr($path, 0, $length) === $publicDir . '/') {
            $path = substr($path, $length);
        }

        return $this->req->getUrlFor('/' . $path);
    }
}
