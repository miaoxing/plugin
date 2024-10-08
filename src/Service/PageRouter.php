<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\BaseService;

class PageRouter extends BaseService
{
    /**
     * @var string
     */
    protected $pageDirGlob = '{pages,plugins/*/pages}';

    /**
     * @var array|null
     */
    protected $pages;

    /**
     * @return array
     */
    public function generatePages(): array
    {
        $dirs = glob($this->pageDirGlob, \GLOB_BRACE | \GLOB_ONLYDIR);
        $pages = [];
        foreach ($dirs as $dir) {
            $pages = $this->scanPages($dir, $dir, $pages);
        }
        return $pages;
    }

    /**
     * @param string $pathInfo
     * @return array|null
     */
    public function match(string $pathInfo): ?array
    {
        $paths = explode('/', ltrim($pathInfo, '/'));
        $pages = $this->getPages();

        // Handle index page
        if ('/' === $pathInfo) {
            $pages = ['/' => $pages];
        }

        $result = $this->matchPaths($paths, $pages);
        if (!$result) {
            return null;
        }

        $params = [];
        foreach ($result['paths'] as $i => $path) {
            if (false !== strpos($path, '[')) {
                preg_match('/\[(.+?)\]/', $path, $matches);
                if ($matches) {
                    $params[$matches[1]] = $paths[$i];
                }
            }
        }

        $path = (isset($result['path']) && $result['path']) ? ($result['path'] . '/') : '';
        return [
            'file' => $path . ltrim(implode('', $result['paths']), '/'),
            'params' => $params,
        ] + $result;
    }

    /**
     * @return array
     */
    public function getPages(): array
    {
        if (null === $this->pages) {
            $this->pages = $this->generatePages();
        }
        return $this->pages;
    }

    /**
     * @param array|null $pages
     * @return $this
     */
    public function setPages(?array $pages = null): self
    {
        $this->pages = $pages;
        return $this;
    }

    /**
     * @return string
     */
    public function getPageDirGlob(): string
    {
        return $this->pageDirGlob;
    }

    /**
     * @param string $pageDirGlob
     * @return $this
     */
    public function setPageDirGlob(string $pageDirGlob): self
    {
        $this->pageDirGlob = $pageDirGlob;
        $this->pages = null;
        return $this;
    }

    /**
     * @param array $urlPaths
     * @param array $filePaths
     * @param int $depth
     * @param array $matches
     * @return array|null
     */
    protected function matchPaths(array $urlPaths, array $filePaths, int $depth = 0, array $matches = []): ?array
    {
        if (!isset($urlPaths[$depth])) {
            return null;
        }

        $isLast = $depth + 1 === count($urlPaths);
        foreach ($filePaths as $filePath => $next) {
            if (!$this->matchPath($urlPaths[$depth], $filePath, $isLast)) {
                continue;
            }

            $hasNext = $this->hasNext($next);
            if ($isLast && $this->isEnded($next, $hasNext)) {
                $matches[] = $filePath;
                if (isset($next['file'])) {
                    $matches[] = '/' . $next['file'];
                }
                return $next + ['paths' => $matches];
            }

            if (!$hasNext) {
                continue;
            }

            $result = $this->matchPaths($urlPaths, $next, $depth + 1, array_merge($matches, [$filePath]));
            if ($result) {
                return $result;
            }
        }

        return null;
    }

    /**
     * @param string $urlPath
     * @param string $filePath
     * @param bool $isLast
     * @return bool
     */
    protected function matchPath(string $urlPath, string $filePath, bool $isLast): bool
    {
        $urlPath = '/' . $urlPath;

        // Ignore config
        if ('/' !== substr($filePath, 0, 1)) {
            return false;
        }

        if ($urlPath === $filePath) {
            return true;
        }

        if ($isLast && ($urlPath . '.php') === $filePath) {
            return true;
        }

        if (false !== strpos($filePath, '[')) {
            return true;
        }

        return false;
    }

    /**
     * @param string|array $pages
     * @return bool
     */
    protected function hasNext($pages): bool
    {
        if (!$pages || !is_array($pages)) {
            return false;
        }
        foreach ($pages as $name => $page) {
            if ('/' === substr($name, 0, 1)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $rootDir
     * @param string $dir
     * @param array $pages
     * @return array
     */
    protected function scanPages(string $rootDir, string $dir, array $pages = []): array
    {
        $files = array_reverse(scandir($dir));

        foreach ($files as $file) {
            if ('.' === $file || '..' === $file) {
                continue;
            }

            $path = '/' . $file;
            if (is_dir($dir . $path)) {
                $result = $this->scanPages($rootDir, $dir . $path);
                // Ignore empty array
                if ($result) {
                    if (isset($pages[$path])) {
                        $pages[$path] = array_merge_recursive($result, $pages[$path]);
                    } else {
                        $pages[$path] = $result;
                    }
                }
                continue;
            }

            if ('index.php' === $file) {
                $pages += ['file' => 'index.php', 'path' => $rootDir];
                continue;
            }

            // TODO better support for views
            $info = pathinfo($file);
            // Ignore file start with underscore
            if ('_' !== substr($info['basename'], 0, 1) && 'php' === $info['extension']) {
                $pages[$path] = ['path' => $rootDir];
            }
        }
        return $pages;
    }

    /**
     * @param string|array $next
     * @param bool $hasNext
     * @return bool
     */
    private function isEnded($next, bool $hasNext): bool
    {
        if (isset($next['file']) || isset($next['name'])) {
            return true;
        }
        return !$hasNext;
    }
}
