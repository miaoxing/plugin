<?php

namespace Model;

use Miaoxing\Plugin\Test\BaseTestCase;
use MiaoxingTest\Plugin\Model\Fixture\TestIp;

/**
 * @mixin \ReqPropMixin
 */
class IpTraitTest extends BaseTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        static::dropTables();

        wei()->schema->table('test_ips')
            ->id()
            ->varBinary('created_ip', 16)
            ->exec();
    }

    public static function tearDownAfterClass(): void
    {
        static::dropTables();
        parent::tearDownAfterClass();
    }

    public static function dropTables()
    {
        wei()->schema->dropIfExists('test_ips');
    }

    public function testSave()
    {
        $ip = TestIp::new()->save();

        $this->assertNotEmpty($ip->get('created_ip'));
    }

    public function testGetIpFromReq()
    {
        $this->req->setServer('REMOTE_ADDR', '1.1.1.1');

        $ip = TestIp::new()->save();

        $this->assertSame('1.1.1.1', $ip->get('created_ip'));
    }
}
