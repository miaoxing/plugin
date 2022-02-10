<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\BaseService;
use Wei\Ret;

abstract class BaseStorage extends BaseService
{
    /**
     * 写入指定文件路径和呢穿挪个
     *
     * 返回内容
     *
     * [
     *   'code' => 0,
     *   'message' => '上传成功'
     *   'url' => 'http://xxx',
     *   'origName' => 'xxx.jpg', // 可选
     *   'size' => 100, // 可选
     *   'width' => 100, // 可选
     *   'height' => 100, // 可选
     *   'md5' => 'abc' // 可选
     * ]
     *
     * @param string $path 文件路径
     * @param string $content 文件内容
     * @param array $options 写入选项
     * @return Ret|array{url: string, origName?: string, size?: int, width?: int, height?: int, md5?: int}
     * @phpstan-return Ret
     * @svc
     */
    abstract protected function write(string $path, string $content, array $options = []): Ret;

    /**
     * Return the URL of path
     */
    abstract protected function getUrl(string $path): string;

    /**
     * 将本地文件写入到文件系统中
     *
     * @param string $file
     * @param array{path?: string} $options
     * @return Ret
     * @svc
     */
    protected function writeFile(string $file, array $options = []): Ret
    {
        $path = $options['path'] ?? $file;
        return $this->write($path, file_get_contents($file), $options);
    }

    /**
     * 将本地文件写入到文件系统中并删除原来的文件
     *
     * @svc
     */
    protected function moveLocal(string $path, array $options = []): Ret
    {
        $ret = $this->writeFile($path, $options);

        $ret->isSuc() && unlink($path);

        return $ret;
    }
}
