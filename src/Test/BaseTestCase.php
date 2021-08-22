<?php

namespace Miaoxing\Plugin\Test;

use Miaoxing\Plugin\BaseService;
use Miaoxing\Plugin\Service\Cls;
use PHPUnit\Framework\MockObject\MockObject;
use Wei\Base;
use Wei\Ret;
use Wei\ServiceTrait;
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

        // 移除被 mock 的服务
        foreach ($this->mockServices as $name) {
            $wei->remove($name);
        }

        $wei->req->clear();
        $wei->req->setOption('gets', []);
        $wei->res->setStatusCode(200);
        $wei->app->setOption('plugin', null);
        $wei->block->setOption('data', []);
    }

    /**
     * 获取服务的 Mock 对象
     *
     * @param string $class
     * @param array<string> $methods
     * @return BaseService&MockObject
     *
     * @phpstan-template T
     * @phpstan-param class-string<BaseService>&class-string<T> $class
     * @phpstan-return MockObject&BaseService&T
     */
    public function getServiceMock(string $class, array $methods = [])
    {
        /** @var MockObject&BaseService&T $service */
        $service = $this->getMockBuilder($class)
            ->onlyMethods($methods)
            ->getMock();

        $this->registerMockServices($class, $service);

        return $service;
    }

    /**
     * 获取模型的 Mock 对象
     *
     * @param string $class
     * @param array<string> $methods
     * @return BaseService&MockObject
     *
     * @phpstan-template T
     * @phpstan-param class-string<BaseService>&class-string<T> $class
     * @phpstan-return MockObject&BaseService&T
     */
    public function getModelServiceMock(string $class, array $methods = [])
    {
        $name = Cls::baseName($class);
        if ('Model' !== $name && 'Model' === substr($name, -5)) {
            $name = substr($name, 0, -5);
        }
        $str = $this->wei->str;
        $table = $str->pluralize($str->snake($name));

        /** @var MockObject&BaseService&T $model */
        $model = $this->getMockBuilder($class)
            ->onlyMethods($methods)
            ->setConstructorArgs([
                [
                    'table' => $table,
                ],
            ])
            ->getMock();

        $this->registerMockServices($class, $model);

        return $model;
    }

    /**
     * 记录 Mock 对象
     *
     * @param string $class
     * @param Base $service
     */
    protected function registerMockServices(string $class, Base $service)
    {
        $name = lcfirst(Cls::baseName($class));
        $service->setOption('wei', $this->wei);
        $this->wei->set($name, $service);
        $this->mockServices[] = $name;
    }

    /**
     * @param Ret $ret
     * @param string $message
     * @param string $assertMessage
     */
    public function assertRetSuc(Ret $ret, $message = null, $assertMessage = null)
    {
        $assertMessage = $this->buildRetMessage($ret, $assertMessage);

        $expected = ['code' => $ret->getOption('defaultSucCode')];
        if (null !== $message) {
            $expected['message'] = $message;
        }

        $this->assertArrayContains($expected, $ret->toArray(), $assertMessage);
    }

    /**
     * @param Ret $ret
     * @param string $message
     * @param string $assertMessage
     * @param mixed $code
     */
    public function assertRetErr(Ret $ret, $message = null, $code = null, $assertMessage = null)
    {
        $assertMessage = $this->buildRetMessage($ret, $assertMessage);
        $expected = [];

        if (null !== $code) {
            $expected['code'] = $code;
        }

        if (null !== $message) {
            $expected['message'] = $message;
        }

        $this->assertArrayContains($expected, $ret->toArray(), $assertMessage);
    }

    /**
     * 测试两个 Ret 的内容是否完全相等
     *
     * @param Ret $expected
     * @param Ret $actual
     * @param string $message
     */
    public function assertSameRet(Ret $expected, Ret $actual, string $message = ''): void
    {
        $this->assertSame($expected->toArray(), $actual->toArray(), $message);
    }

    public static function assertArrayContains($subset, $array, $message = '')
    {
        $array = array_intersect_key($array, array_flip(array_keys($subset)));
        parent::assertEquals($subset, $array, $message);
    }

    /**
     * 根据类名,查找HTML文本中的节点
     *
     * @param string $html
     * @param string $class
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
        return $assertMessage . ' ret is ' . json_encode($ret, \JSON_UNESCAPED_UNICODE);
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
