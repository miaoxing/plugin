<?php

namespace MiaoxingTest\Plugin\Fixture;

use miaoxing\plugin\BaseModel;

/**
 * @property string test_user_id
 */
class TestProfile extends BaseModel
{
    protected $table = 'test_profiles';

    protected $enableProperty = true;
}
