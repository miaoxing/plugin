<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Service\Jwt;
use Miaoxing\Plugin\Test\BaseTestCase;

/**
 * @mixin \ReqMixin
 */
class JwtTest extends BaseTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $dir = sys_get_temp_dir();
        Jwt::setPrivateKey($dir . '/private.key')
            ->setPublicKey($dir . '/public.key')
            ->generateDefaultKeys();
    }

    public static function tearDownAfterClass(): void
    {
        unlink(Jwt::getPrivateKey());
        unlink(Jwt::getPublicKey());
        parent::tearDownAfterClass();
    }

    public function testGenerate()
    {
        $token = Jwt::generate(['test' => '1']);

        $this->assertStringContainsString('.', $token);
    }

    public function testVerify()
    {
        $token = Jwt::generate(['test' => '1']);
        $ret = Jwt::verify($token);
        $this->assertRetSuc($ret);
        $this->assertSame('1', $ret['data']['test']);
    }

    public function testVerifyEmpty()
    {
        $ret = Jwt::verify('');
        $this->assertRetErr($ret, 'Token 不能为空');
    }

    public function testVerifyParseFail()
    {
        $ret = Jwt::verify('123');
        $this->assertRetErr($ret, '解析 Token 失败');
    }

    public function testVerifyExpired()
    {
        $token = Jwt::generate(['test' => '1'], -1);
        $this->assertRetErr(Jwt::verify($token), 'Token 已过期', Jwt::CODE_EXPIRED);
    }

    public function testVerifySignErr()
    {
        $token = Jwt::generate(['test' => '1']);
        $ret = Jwt::verify($token . '1');
        $this->assertRetErr($ret, 'Token 签名错误');
    }

    public function testVerifyDataFail()
    {
        $token = Jwt::generate(['test' => '1']);

        $origPort = $this->req->getServer('SERVER_PORT');
        $this->req->setServer('SERVER_PORT', (string) ($origPort + 1));

        $ret = Jwt::verify($token);
        $this->assertRetErr($ret, 'Token 内容错误');
        $this->req->setServer('SERVER_PORT', $origPort);
    }
}
