<?php

namespace MiaoxingTest\Plugin\Auth;

use Miaoxing\Plugin\Auth\JwtAuth;
use Miaoxing\Plugin\Service\UserModel;
use Miaoxing\Plugin\Test\BaseTestCase;

class JwtAuthTest extends BaseTestCase
{
    public function testLogin()
    {
        $auth = new JwtAuth();
        $auth->login(UserModel::new());
        $this->assertTrue($auth->isLogin());
    }

    public function testGetData()
    {
        $auth = new JwtAuth();
        $auth->login(UserModel::new()->set('id', 999));
        $this->assertSame('999', $auth->getData()['id']);
    }
}
