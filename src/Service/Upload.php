<?php

namespace Miaoxing\Plugin\Service;

class Upload extends \Wei\Upload
{
    /**
     * 允许的图片类型的扩展名
     *
     * @var array
     */
    protected $allowedImageExts = [
        'jpg',
        'jpeg',
        'png',
        'bmp',
        'gif',
    ];

    /**
     * 获取图片的扩展名
     *
     * @svc
     */
    protected function getAllowedImageExts(): array
    {
        return $this->allowedImageExts;
    }

    /**
     * 检查扩展名是否为允许的图片类型
     *
     * @svc
     */
    protected function isAllowedImageExt(string $ext): bool
    {
        return in_array($ext, $this->allowedImageExts, true);
    }

    /**
     * 获取所有允许上传的文件扩展名
     *
     * @svc
     */
    protected function getAllowedExts(): array
    {
        return array_merge(
            $this->allowedImageExts
        );
    }

    /**
     * 上传图片文件
     *
     * @svc
     */
    protected function saveImage(array $options = []): \Wei\Ret
    {
        return $this->save($options + ['exts' => $this->getAllowedImageExts()]);
    }
}
