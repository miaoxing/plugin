<?php

namespace Miaoxing\Plugin\Service;

/**
 * @todo 增加单元测试,rev功能移到rev插件中
 */
class Asset extends \Wei\Asset
{
    /**
     * Whether enable the revision map
     *
     * @var bool
     */
    protected $enableRev = null;

    /**
     * The URL prefix of revision file
     *
     * @var string
     */
    protected $revPrefix = '/dist/';

    /**
     * The JSON file that contains the revision map
     *
     * @var string
     */
    protected $revFile = 'data/rev/manifest.json';

    /**
     * An array that the key is file path and the value is revision(file content hash)
     *
     * array(
     *   'assets/article.js' => 'a123b456',
     *   'assets/article.css' => 'c123d456'
     * )
     *
     * @var null|array
     */
    protected $revMap = null;

    /**
     * @var callable
     */
    protected $locateFile;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $options = [])
    {
        parent::__construct($options);

        if ($this->enableRev == null) {
            if (!$this->wei->isDebug()) {
                $this->enableRev = true;
            }
            if (isset($this->wei->request['rev'])) {
                $this->enableRev = (bool) $this->wei->request['rev'];
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke($file, $version = true)
    {
        $origFile = $file;
        if ($this->enableRev) {
            $file = $this->getRevFile($file);
        } else {
            $file = $this->getFile($file);
        }

        // 如果还是原来的文件,附加前缀
        if ($file == $origFile) {
            $file = $this->getFile($file);
        }

        if ($version && $this->version) {
            $file = $this->addVersion($file);
        }

        return $file;
    }

    /**
     * Convert file name by callback
     *
     * @param string $file
     * @return mixed
     */
    protected function locateFile($file)
    {
        $this->locateFile && $file = call_user_func($this->locateFile, $file);

        return $file;
    }

    /**
     * Returns the file path with base URL
     *
     * @param string $file
     * @return string
     */
    public function getFile($file)
    {
        return $this->baseUrl . $this->locateFile($file);
    }

    /**
     * Returns rev file content that parsed to array
     *
     * @return array
     */
    public function getRevMap()
    {
        if ($this->revMap === null) {
            if (is_file($this->revFile)) {
                $this->revMap = (array) json_decode(file_get_contents($this->revFile));
            } else {
                $this->revMap = [];
            }
        }

        return $this->revMap;
    }

    /**
     * Add rev map from file
     *
     * @param string $file
     * @return $this
     */
    public function addRevFile($file)
    {
        // Load default rev file
        $this->getRevMap();

        if (is_file($file)) {
            $this->revMap = json_decode(file_get_contents($file), true) + $this->revMap;
        }

        return $this;
    }

    /**
     * Returns the file path contains revision
     *
     * eg 'assets/article.js' => 'assets/article-a123b456.js'
     *
     * @param string $file
     * @return string
     */
    public function getRevFile($file)
    {
        $map = $this->getRevMap();
        if (isset($map[$file])) {
            $index = strrpos($file, '.');
            $file = $this->revPrefix . substr($file, 0, $index) . '-' . $map[$file] . substr($file, $index);
        }

        return $file;
    }

    /**
     * Adds version to specified URL
     *
     * @param string $url
     * @return string
     */
    public function addVersion($url)
    {
        return $url . ((false === strpos($url, '?')) ? '?' : '&') . 'v=' . $this->version;
    }

    /**
     * @return bool
     */
    public function isEnableRev()
    {
        return $this->enableRev;
    }

    /**
     * @return string
     */
    public function getRevPrefix()
    {
        return $this->revPrefix;
    }

    /**
     * 使用imageView2功能为图片生成缩略图
     *
     * 临时放在asset服务中,成熟后会拆出为独立服务
     *
     * @param string $url
     * @param int $size 边长
     * @return string
     */
    public function thumb($url, $size = 100)
    {
        return $url . '?imageView2/2/w/' . $size . '/h/' . $size . '/q/75';
    }
}
