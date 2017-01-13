<?php

namespace Miaoxing\Plugin\Test;

use Miaoxing\Plugin\Test\BaseFixture;

class BaseTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Wei\Wei|\miaoxing\plugin\BaseService
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

    protected function setUp()
    {
        $this->wei = wei();

        // 将当前测试用例作为一个服务,允许其他服务调用
        $this->wei->set('testCase', $this);
    }

    protected function tearDown()
    {
        $wei = $this->wei;

        // 还原被mock的服务
        foreach ($this->mockServices as $name => $service) {
            $wei->$name = $service;
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
        $service = $this->wei->$name;
        $this->mockServices[$name] = $service;

        $class = get_class($service);
        if (!method_exists($this, 'createMock')) {
            $mock = $this->getMock($class, $methods, $arguments);
        } else {
            // For PHPUnit 5.4+
            $mock = $this->createMock($class);
        }

        $this->wei->$name = $mock;

        return $mock;
    }

    public function getModelServiceMock($name, $methods = [], array $arguments = [])
    {
        // TODO PHPUnit?空方法会导致其他方法都返回null,
        $methods || $methods = ['__fake'];

        $model = $this->getServiceMock($name, $methods, $arguments);

        // TODO 通过db服务调用getTableFields会出现错误 Illegal offset type in isset or empty in services/Db.php on line 16
        $fields = wei()->appDb->getTableFields($model->getTable());
        $model->setOption('fields', $fields);

        return $model;
    }

    public function assertRetSuc(array $ret, $message = null, $assertMessage = null)
    {
        $assertMessage = $this->buildRetMessage($ret, $assertMessage);

        $expected = ['code' => 1];
        if ($message !== null) {
            $expected['message'] = $message;
        }

        $this->assertArraySubset($expected, $ret, true, $assertMessage);
    }

    public function assertRetErr(array $ret, $code, $message = null, $assertMessage = null)
    {
        $assertMessage = $this->buildRetMessage($ret, $assertMessage);

        $expected = ['code' => $code];
        if ($message !== null) {
            $expected['message'] = $message;
        }

        $this->assertArraySubset($expected, $ret, true, $assertMessage);
    }

    protected function buildRetMessage($ret, $assertMessage = null)
    {
        return $assertMessage . ' ret is ' . json_encode($ret, JSON_UNESCAPED_UNICODE);
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
     * @return BaseFixture
     */
    protected function getFixture()
    {
        if (!$this->fixture) {
            $class = sprintf('plugins\%s\tests\Fixture', $this->getPluginName());
            $this->fixture = new $class([
                'wei' => $this->wei,
                'test' => $this,
            ]);
        }

        return $this->fixture;
    }

    /**
     * 获取当前测试类所在的插件名称
     *
     * @return string
     */
    protected function getPluginName()
    {
        // 类名如 miaoxing\app\tests\PluginTest
        // 分为3部分取第2部分
        return explode('\\', get_class($this), 3)[1];
    }

    /**
     * @param string $message
     * @param string $args
     */
    public function step($message, $args = null)
    {
        $args = func_get_args();
        if (count($args) > 1) {
            array_shift($args);
            $message = vsprintf($message, $args);
        }
        ResultPrinter::addMessage($message);
    }
}
