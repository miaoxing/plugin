<?php

namespace Miaoxing\Plugin\Test;

use Miaoxing\Services\Service\ServiceTrait;
use Wei\Wei;

abstract class BaseTestCase extends \PHPUnit\Framework\TestCase
{
    use ServiceTrait;

    /**
     * @var Wei
     */
    protected $wei;

    /**
     * @var array
     */
    protected $mockServices = [];

    /**
     * @var BaseFixture
     */
    protected $fixture;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->wei = wei();

        // 将当前测试用例作为一个服务,允许其他服务调用
        $this->wei->set('testCase', $this);
    }

    protected function tearDown(): void
    {
        $wei = $this->wei;

        // 还原被mock的服务
        foreach ($this->mockServices as $name => $service) {
            $wei->{$name} = $service;
        }

        $wei->request->clear();
        $wei->request->setOption('gets', []);
        $wei->response->setStatusCode(200);
        $wei->app->setOption('plugin', null);
        $wei->block->setOption('data', []);
    }

    /**
     * 获取服务的mock对象
     *
     * @param string $name
     * @param array $methods
     * @param array $arguments
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function getServiceMock($name, $methods = [], array $arguments = [])
    {
        $service = $this->wei->{$name};
        $this->mockServices[$name] = $service;

        $class = get_class($service);
        if (!method_exists($this, 'createMock')) {
            $mock = $this->getMock($class, $methods, $arguments);
        } else {
            // For PHPUnit 5.4+
            $mock = $this->createMock($class);
        }

        $this->wei->{$name} = $mock;

        return $mock;
    }

    public function getModelServiceMock($name, $methods = [], array $arguments = [])
    {
        $table = wei()->{$name}()->getTable();

        // TODO PHPUnit?空方法会导致其他方法都返回null,
        $methods || $methods = ['__fake'];

        $model = $this->getServiceMock($name, $methods, $arguments);

        // TODO 通过db服务调用getTableFields会出现错误 Illegal offset type in isset or empty in services/Db.php on line 16
        $fields = wei()->db->getTableFields($table);
        $model->setOption('fields', $fields);

        return $model;
    }

    public function assertRetSuc(array $ret, $message = null, $assertMessage = null)
    {
        $assertMessage = $this->buildRetMessage($ret, $assertMessage);

        $expected = ['code' => 1];
        if (null !== $message) {
            $expected['message'] = $message;
        }

        $this->assertArrayContains($expected, $ret, $assertMessage);
    }

    public function assertRetErr(array $ret, $code, $message = null, $assertMessage = null)
    {
        $assertMessage = $this->buildRetMessage($ret, $assertMessage);

        $expected = ['code' => $code];
        if (null !== $message) {
            $expected['message'] = $message;
        }

        $this->assertArrayContains($expected, $ret, $assertMessage);
    }

    public static function assertArrayContains($subset, $array, $message = '')
    {
        $array = array_intersect_key($array, array_flip(array_keys($subset)));
        parent::assertEquals($subset, $array, $message);
    }

    /**
     * 根据类名,查找HTML文本中的节点
     *
     * @param $html
     * @param $class
     * @return \DOMNodeList
     * @todo 换为DomCrawler或codecption
     */
    public function findByClass($html, $class)
    {
        $dom = new \DomDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();
        $finder = new \DomXPath($dom);

        return $finder->query("//*[contains(@class, '$class')]");
    }

    /**
     * @param string $message
     * @param string $args
     * @deprecated 使用 asset 的 message
     */
    public function step($message, $args = null)
    {
        // do nothing
    }

    protected function buildRetMessage($ret, $assertMessage = null)
    {
        return $assertMessage . ' ret is ' . json_encode($ret, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 获取当前测试类所在的插件名称
     *
     * @return string
     */
    protected function getPluginName()
    {
        // 类名如 MiaoxingTest\App\PluginTest
        // 分为3部分取第2部分
        return explode('\\', static::class, 3)[1];
    }
}
