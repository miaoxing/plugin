<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\BaseService;
use Miaoxing\Plugin\Model\QueryBuilderCacheTrait;
use Miaoxing\Plugin\Model\QueryBuilderPropsTrait;
use Miaoxing\Plugin\Model\QueryBuilderTrait;

/**
 * A SQL query builder class
 *
 * @author Twin Huang <twinhuang@qq.com>
 */
class QueryBuilder extends BaseService
{
    use QueryBuilderPropsTrait;
    use QueryBuilderTrait;
    use QueryBuilderCacheTrait;

    /**
     * @var bool
     */
    protected static $createNewInstance = true;
}
