<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\BaseService;

class Fs extends BaseService
{
    /**
     * Create if directory not exists
     *
     * @svc
     */
    protected function ensureDir(string $dir, int $permissions = 0777): self
    {
        if (!is_dir($dir)) {
            mkdir($dir, $permissions, true);
        }
        return $this;
    }

    /**
     * Get the file extension name
     *
     * @svc
     */
    protected function getExt(string $file, ?string $default = null): ?string
    {
        $pos = strrpos($file, '.');
        if (false !== $pos) {
            return strtolower(substr($file, $pos + 1));
        } else {
            return $default;
        }
    }

    /**
     * Remove "public/" in path
     *
     * @param string $path
     * @return string
     * @svc
     */
    protected function stripPublic(string $path): string
    {
        $publicDir = 'public/';
        $length = strlen($publicDir);

        if (substr($path, 0, $length) === $publicDir) {
            return substr($path, $length);
        }

        return $path;
    }
}
