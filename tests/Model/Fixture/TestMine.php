<?php

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Model\MineTrait;
use Wei\BaseModel;
use Wei\ModelTrait;

/**
 * @property int|null $id
 */
class TestMine extends BaseModel
{
    use MineTrait;
    use ModelTrait;
}
