<?php

namespace Miaoxing\Plugin\Model;

trait CamelCaseConditionTrait
{
    protected function processCondition($conditions, $params, $types)
    {
        if (is_array($conditions)) {
            $data = [];
            foreach ($conditions as $column => $value) {
                if (preg_match('/[A-Z]/', $column)) {
                    $this->logger->info('Found camel case column', debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5));
                    $data[$this->snake($column)] = $value;
                } else {
                    $data[$column] = $value;
                }
            }
            $conditions = $data;
        }

        return parent::processCondition($conditions, $params, $types);
    }
}
