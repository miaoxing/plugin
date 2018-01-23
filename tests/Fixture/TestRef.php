<?php

namespace MiaoxingTest\Plugin\Fixture;

use Miaoxing\Plugin\BaseModelV2;

class TestRef extends BaseModelV2
{
    protected $casts = [
        'id' => 'int',
        'json' => 'json',
    ];
}
