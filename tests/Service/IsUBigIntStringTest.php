<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Service\IsUBigIntString;
use Miaoxing\Plugin\Test\BaseTestCase;

class IsUBigIntStringTest extends BaseTestCase
{
    public function testEmptyString()
    {
        $this->assertRetSuc(IsUBigIntString::check(''));
    }

    public function testZero()
    {
        $this->assertRetSuc(IsUBigIntString::check(0));
    }

    public function testNegative()
    {
        $this->assertRetErr(IsUBigIntString::check('-1'));
    }
}
