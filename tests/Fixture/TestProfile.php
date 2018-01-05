<?php

namespace MiaoxingTest\Plugin\Fixture;

use Miaoxing\Plugin\BaseModel;

/**
 * @property string test_user_id
 */
class TestProfile extends BaseModel
{
    protected $enableProperty = true;

    protected $tableV2 = true;
}
