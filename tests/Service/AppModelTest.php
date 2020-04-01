<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Test\BaseTestCase;

class AppModelTest extends BaseTestCase
{
    public function testGetIdByDomain()
    {
        $prefix = 'appDomain:';
        $app = wei()->appModel()->findOrInitBy(['domain' => 't.test.com']);

        $app->save([
            'name' => 'domain',
            'domain' => '',
        ]);

        wei()->cache->remove($prefix . 't.test.com');
        $this->assertFalse(wei()->appModel->getIdByDomain('t.test.com'));

        wei()->cache->remove($prefix . 't.test.com');
        $app->save(['domain' => 't.test.com']);

        $this->assertEquals('domain', wei()->appModel->getIdByDomain('t.test.com'));

        $app->destroy();
    }
}
