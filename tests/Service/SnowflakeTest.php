<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Service\Snowflake;
use Miaoxing\Plugin\Test\BaseTestCase;

class SnowflakeTest extends BaseTestCase
{
    public function testReturnType()
    {
        $id = Snowflake::next();
        $this->assertIsString($id);
        $this->assertIsNumeric($id);
    }

    public function testOrder()
    {
        $id = Snowflake::next();
        $id2 = Snowflake::next();
        $id3 = Snowflake::next();

        $this->assertGreaterThan($id, $id2);
        $this->assertGreaterThan($id2, $id3);
    }
}
