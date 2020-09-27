<?php

namespace MiaoxingTest\Plugin\Auth;

use Miaoxing\Plugin\Auth\SessionAuth;
use Miaoxing\Plugin\Test\BaseTestCase;

/**
 * @mixin \SessionMixin
 */
class SessionAuthTest extends BaseTestCase
{
    public function testGetDataWontCauseUserKeyToNull()
    {
        $auth = new SessionAuth();
        $data = $auth->getData();
        $this->assertSame([], $data);
        $this->assertArrayNotHasKey('user', $this->session);
    }

    public function testEmptyUserSession()
    {
        $auth = new SessionAuth();
        $this->session['user'] = [];
        $this->assertFalse($auth->isLogin());
    }
}