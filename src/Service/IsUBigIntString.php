<?php

namespace Miaoxing\Plugin\Service;

use Wei\IsUBigInt;

/**
 * @experimental
 */
class IsUBigIntString extends IsUBigInt
{
    /**
     * {@inheritdoc}
     */
    protected function doValidate($input)
    {
        if ('' === $input) {
            return true;
        }
        return parent::doValidate($input);
    }
}
