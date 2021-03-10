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
}
