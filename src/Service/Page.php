<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Config\ConfigTrait;
use Miaoxing\Plugin\BaseService;
use Wei\Block;

/**
 * @property-read Block $block
 * @property-read View $view
 * @property-read Str $str
 * @property-read Asset $asset
 * @method string asset(string $file)
 * @property-read Asset $wpAsset
 * @method string wpAsset(string $file)
 * @property bool $showTitleInHeader
 */
class Page extends BaseService
{
    use ConfigTrait;

    protected $configs = [
        'showTitleInHeader' => [
            'default' => false,
        ],
    ];

    /**
     * @var string
     */
    protected $title;

    /**
     * @var bool
     */
    protected $header = true;

    /**
     * @var string
     */
    protected $headerTitle = '';

    /**
     * @var bool
     */
    protected $footer = true;

    /**
     * @var array
     */
    protected $jsFiles = [];

    /**
     * @var array
     */
    protected $cssFiles = [];

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        // TODO setting 转为 config
        return $this->showTitleInHeader ? wei()->setting('site.title') : ($this->title ?: wei()->setting('site.title'));
    }

    /**
     * @return bool
     */
    public function hasHeader()
    {
        return $this->header;
    }

    /**
     * @return bool
     */
    public function hasFooter()
    {
        return $this->footer;
    }

    /**
     * @return $this
     */
    public function showHeader()
    {
        $this->header = true;
        return $this;
    }

    /**
     * @return $this
     */
    public function hideHeader()
    {
        $this->header = false;
        return $this;
    }

    /**
     * @return $this
     */
    public function showFooter()
    {
        $this->footer = true;
        return $this;
    }

    /**
     * @return $this
     */
    public function hideFooter()
    {
        $this->footer = false;
        return $this;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setHeaderTitle($title)
    {
        $this->headerTitle = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getHeaderTitle()
    {
        return $this->headerTitle ?: ($this->showTitleInHeader ? $this->title : '');
    }

    public function addJs($src)
    {
        if (is_array($src)) {
            foreach ($src as $item) {
                $this->addJs($item);
            }
        } else {
            $this->jsFiles[] = $src;
        }
        return $this;
    }

    public function prependJs($src)
    {
        array_unshift($this->jsFiles, $src);
        return $this;
    }

    public function addCss($href)
    {
        $this->cssFiles[] = $href;
        return $this;
    }

    public function prependCss($href)
    {
        array_unshift($this->cssFiles, $href);
        return $this;
    }

    public function addAsset($asset)
    {
        $asset = $this->asset($asset);
        $ext = pathinfo($asset, PATHINFO_EXTENSION);
        switch ($ext) {
            case 'css':
                return $this->addCss($asset);

            case 'js':
            default:
                return $this->addJs($asset);
        }
    }

    public function renderHead()
    {
        echo $this->renderCss();
        $this->event->trigger('style');
        echo $this->block->get('css');
    }

    public function renderFooter()
    {
        echo $this->renderJs();
        $this->event->trigger('script');
        echo $this->block->get('js');
        $this->event->trigger('bodyEnd');
    }

    public function renderCss()
    {
        $html = '';
        foreach ($this->cssFiles as $file) {
            $html .= ' <link rel="stylesheet" href="' . $file . '" />' . "\n";
        }
        return $html;
    }

    public function renderJs()
    {
        $html = '';
        foreach ($this->jsFiles as $file) {
            $html .= '<script src="' . $file . '"></script>' . "\n";
        }
        return $html;
    }

    /**
     * 增加插件的资源
     *
     * @param string|null $action
     * @param string|null $plugin
     * @param bool $hasCss
     * @return $this
     * @deprecated
     */
    public function addPluginAsset($action = null, $plugin = null, $hasCss = true)
    {
        // 1. 设置路由
        $this->initRoute($action);

        // 2. 加载插件的版本映射表
        // 目前正常只会加载一次，不用缓存
        $plugin || $plugin = $this->app->getPlugin();
        $this->wpAsset->addRevFile('dist2/' . $plugin . '-assets-hash.json');

        // 3. 加载css和js文件
        $hasCss && $this->addCss($this->wpAsset($plugin . '.css'));
        $this->addJs($this->wpAsset($plugin . '.js'));

        return $this;
    }

    public function addPluginAssets($plugin = 'app')
    {
        $this->wpAsset->addRevFile('dist2/' . $plugin . '-assets-hash.json');
        return $this->prependCss($this->wpAsset($plugin . '.css'))
            ->prependJs($this->wpAsset($plugin . '-manifest.js'))
            ->prependJs($this->wpAsset($plugin . '.js'));
    }

    protected function initRoute($action)
    {
        $route = $this->app->getController() . '/' . ($action ?: $this->app->getAction());
        $route = $this->str->dash($route);

        // 配合admin.js加载对应的容器
        $js = $this->view->get('js');
        $js['route'] = $route;
        $this->view->assign('js', $js);
    }
}
