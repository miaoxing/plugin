<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\BaseService;
use Wei\Schema;

/**
 * @experimental may be rename, return Column object instead of Schema
 */
class PresetColumns extends BaseService
{
    /**
     * @var Schema
     */
    protected $schema;

    public function __construct(array $options = [])
    {
        parent::__construct($options);
        // @phpstan-ignore-next-line Xxx::$schema (Wei\Schema) does not accept Wei\Base.
        $this->schema = $this->wei->newInstance('schema');
    }

    /**
     * @svc
     */
    protected function appId(): Schema
    {
        return $this->schema->uBigInt('app_id')->comment('应用编号');
    }

    /**
     * @svc
     */
    protected function userId(): Schema
    {
        return $this->schema->uBigInt('user_id')->comment('用户编号');
    }

    /**
     * @svc
     */
    protected function sort(): Schema
    {
        return $this->schema->smallInt('sort')->defaults(50)->comment('顺序，从大到小排列');
    }

    /**
     * @svc
     */
    protected function isEnabled(): Schema
    {
        return $this->schema->bool('is_enabled')->defaults(1)->comment('是否启用');
    }
}
