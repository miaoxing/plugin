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
        'svg',
    ];

    /**
     * 各系统支持的音频类型的扩展名
     *
     * @var string[]
     * @link https://developers.weixin.qq.com/miniprogram/dev/api/media/audio/InnerAudioContext.html#%E6%94%AF%E6%8C%81%E6%A0%BC%E5%BC%8F
     */
    protected $allowedAudioExts = [
        'mp3',
        'm4a',
        'wav',
        'aac',
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
            $this->allowedImageExts,
            $this->allowedAudioExts
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

    /**
     * 获取音频的扩展名
     *
     * @svc
     */
    protected function getAllowedAudioExts(): array
    {
        return $this->allowedAudioExts;
    }

    /**
     * 检查扩展名是否为允许的音频类型
     *
     * @svc
     */
    protected function isAllowedAudioExt(string $ext): bool
    {
        return in_array($ext, $this->allowedAudioExts, true);
    }

    /**
     * 上传音频文件
     *
     * @svc
     */
    protected function saveAudio(array $options = []): \Wei\Ret
    {
        return $this->save($options + ['exts' => $this->getAllowedAudioExts()]);
    }
}
