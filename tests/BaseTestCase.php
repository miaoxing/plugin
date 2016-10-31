<?php

namespace miaoxing\plugin\tests;

use miaoxing\app\tests\BaseFixture;
use tests\ResultPrinter;

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
        $mock = $this->getMock(get_class($service), $methods, $arguments);
        $this->wei->$name = $mock;

        return $mock;
    }

    public function getModelServiceMock($name, $methods = [], array $arguments = [])
    {
        // FIXME PHPUnit?空方法会导致其他方法都返回null,
        $methods || $methods = ['__fake'];

        $model = $this->getServiceMock($name, $methods, $arguments);

        // FIXME 通过db服务调用getTableFields会出现错误 Illegal offset type in isset or empty in services/Db.php on line 16
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
        if ($assertMessage === true) {
            $assertMessage = '返回"%message%"';
        }
        if ($assertMessage) {
            $assertMessage = str_replace(['%message%'], [$ret['message']], $assertMessage);
        }

        return $assertMessage;
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
     * {@inheritdoc}
     */
    /*public static function assertThat($value, \PHPUnit_Framework_Constraint $constraint, $message = '')
    {
        parent::assertThat($value, $constraint, $message);
        if ($message) {
            ResultPrinter::addPassMessage($message);
        }
    }*/

    protected function step($message)
    {
        ResultPrinter::addPassMessage($message);
    }
}
