<?php

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Model\SoftDeleteTrait;
use Miaoxing\Plugin\Service\Model;

/**
 * @property string|null $id
 * @property string|null $deleted_at
 */
class TestSoftDelete extends Model
{
    use SoftDeleteTrait;
}
