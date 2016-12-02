<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Test\BaseTestCase;

/**
 * 设置服务
 */
class SettingTest extends BaseTestCase
{
    /**
     * 获取设置值
     */
    public function testGetValue()
    {
        $this->step('获取不存在的设置,返回null');
        $value = wei()->setting('not-exists');
        $this->assertNull($value);

        $this->step('获取不存在的设置,返回指定的默认值');
        $value = wei()->setting('not-exists', 'default');
        $this->assertEquals('default', $value);
    }

    /**
     * 更改设置值
     */
    public function testSetValue()
    {
        $this->step('设置测试值为空会生效');
        wei()->setting->setValue('id', '');
        $this->assertEmpty(wei()->setting('id'));

        $this->step('设置测试值为value会生效');
        wei()->setting->setValue('id', 'value');
        $this->assertEquals('value', wei()->setting('id'));
    }

    /**
     * 批量获取和更新设置值
     */
    public function testGetAndSetValues()
    {
        $this->step('批量更新值为空会生效');
        wei()->setting->setValues([
            'a' => '',
            'b' => '',
        ]);
        $this->assertEquals([
            'a' => '',
            'b' => '',
        ], wei()->setting->getValues(['a', 'b']));

        $this->step('批量更新值为指定值会生效');
        wei()->setting->setValues([
            'a' => 'c',
            'b' => 'd',
        ]);
        $this->assertEquals([
            'a' => 'c',
            'b' => 'd',
        ], wei()->setting->getValues(['a', 'b']));
    }
}
