<?php

namespace Miaoxing\Plugin\Model;

use Miaoxing\Plugin\Service\Snowflake;

trait SnowflakeTrait
{
    public static function bootSnowflakeTrait()
    {
        static::onModelEvent('beforeCreate', 'generateSnowflakeId');
    }

    /**
     * Generate snowflake id if id don't have value
     *
     * @throws \Exception
     */
    protected function generateSnowflakeId()
    {
        if (!$this->id) {
            $this->id = Snowflake::next();
        }
    }
}
