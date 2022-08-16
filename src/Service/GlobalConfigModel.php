<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\BaseModel;
use Miaoxing\Plugin\Metadata\GlobalConfigTrait;
use Miaoxing\Plugin\Model\ModelTrait;
use Wei\Model\SoftDeleteTrait;

class GlobalConfigModel extends BaseModel
{
    use GlobalConfigTrait;
    use ModelTrait;
    use SoftDeleteTrait;
}
